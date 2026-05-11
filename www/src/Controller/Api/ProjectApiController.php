<?php

namespace App\Controller\Api;

use App\Entity\AppelDeFonds;
use App\Repository\AppelDeFondsRepository;
use App\Repository\FormationRepository;
use App\Repository\ProjetRepository;
use App\Repository\SessionFormationRepository;
use App\Service\BudgetCalculator;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1', name: 'api_v1_')]
#[OA\Tag(name: 'Projets')]
class ProjectApiController extends AbstractController
{
    public function __construct(
        private ProjetRepository $projetRepository,
        private SessionFormationRepository $sessionRepository,
        private FormationRepository $formationRepository,
        private AppelDeFondsRepository $appelDeFondsRepository,
        private BudgetCalculator $budgetCalculator,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
    ) {}

    #[Route('/projects', name: 'projects', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/projects',
        summary: 'Liste toutes les balances financières des projets',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des projets avec leur balance financière',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'project_id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Projet Formation PHP'),
                            new OA\Property(property: 'budget_initial', type: 'number', format: 'float', example: 50000.00),
                            new OA\Property(property: 'total_spent', type: 'number', format: 'float', example: 12500.00),
                            new OA\Property(property: 'balance', type: 'number', format: 'float', example: 37500.00),
                            new OA\Property(property: 'budget_used_percentage', type: 'number', format: 'float', example: 25.0),
                            new OA\Property(property: 'alert_threshold', type: 'number', format: 'float', example: 40000.00),
                            new OA\Property(property: 'alert_triggered', type: 'boolean', example: false),
                            new OA\Property(property: 'total_appels_de_fonds', type: 'number', format: 'float', example: 5000.00),
                            new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Clé API manquante ou invalide'),
        ]
    )]
    public function list(): JsonResponse
    {
        $projets = $this->projetRepository->findAll();
        $data = array_map(fn($projet) => $this->buildBalanceData($projet), $projets);

        return $this->json($data);
    }

    #[Route('/projects/{id}/balance', name: 'project_balance', methods: ['GET'])]
    #[OA\Get(
        path: '/api/v1/projects/{id}/balance',
        summary: 'Balance financière d\'un projet',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID du projet', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Balance financière du projet',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'project_id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Projet Formation PHP'),
                        new OA\Property(property: 'budget_initial', type: 'number', format: 'float', example: 50000.00),
                        new OA\Property(property: 'total_spent', type: 'number', format: 'float', example: 12500.00),
                        new OA\Property(property: 'balance', type: 'number', format: 'float', example: 37500.00),
                        new OA\Property(property: 'budget_used_percentage', type: 'number', format: 'float', example: 25.0),
                        new OA\Property(property: 'alert_threshold', type: 'number', format: 'float', example: 40000.00),
                        new OA\Property(property: 'alert_triggered', type: 'boolean', example: false),
                        new OA\Property(property: 'total_appels_de_fonds', type: 'number', format: 'float', example: 5000.00),
                        new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                        new OA\Property(
                            property: 'client',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Client SA'),
                            ]
                        ),
                        new OA\Property(
                            property: 'referent',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Jean Dupont'),
                                new OA\Property(property: 'email', type: 'string', example: 'jean.dupont@example.com'),
                            ]
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 401, description: 'Clé API manquante ou invalide'),
            new OA\Response(response: 404, description: 'Projet introuvable'),
        ]
    )]
    public function balance(int $id): JsonResponse
    {
        $projet = $this->projetRepository->find($id);

        if (!$projet) {
            return $this->json(['error' => 'Projet introuvable.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->buildBalanceData($projet));
    }

    #[Route('/projects/{id}/appels-de-fonds', name: 'project_create_appel', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/projects/{id}/appels-de-fonds',
        summary: 'Créer un appel de fonds pour un projet',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID du projet', schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['montantdemande'],
                properties: [
                    new OA\Property(property: 'montantdemande', type: 'number', format: 'float', example: 5000.00, description: 'Montant demandé en euros (doit être positif)'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Appel de fonds créé avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'appel_id', type: 'integer', example: 5),
                        new OA\Property(property: 'project_id', type: 'integer', example: 1),
                        new OA\Property(property: 'montantdemande', type: 'number', format: 'float', example: 5000.00),
                        new OA\Property(property: 'datedemande', type: 'string', format: 'date', example: '2026-05-11'),
                        new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 401, description: 'Clé API manquante ou invalide'),
            new OA\Response(response: 404, description: 'Projet introuvable'),
        ]
    )]
    public function createAppelDeFonds(int $id, Request $request): JsonResponse
    {
        $projet = $this->projetRepository->find($id);

        if (!$projet) {
            return $this->json(['error' => 'Projet introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Corps de requête JSON invalide.'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($data['montantdemande'] ?? null, [
            new Assert\NotNull(message: 'Le champ montantdemande est requis.'),
            new Assert\Positive(message: 'Le montant doit être positif.'),
        ]);

        if (count($errors) > 0) {
            return $this->json(
                ['errors' => array_map(fn($e) => $e->getMessage(), iterator_to_array($errors))],
                Response::HTTP_BAD_REQUEST
            );
        }

        $appel = new AppelDeFonds();
        $appel->setMontantdemande((float) $data['montantdemande']);
        $appel->setDatedemande(new \DateTime());
        $appel->setProjet($projet);

        $this->em->persist($appel);
        $this->em->flush();

        return $this->json($this->buildAppelData($appel), Response::HTTP_CREATED);
    }

    private function buildBalanceData(object $projet): array
    {
        $sessions    = $this->sessionRepository->findBy(['projet' => $projet]);
        $totalDepense = 0.0;

        foreach ($sessions as $session) {
            $formations = $this->formationRepository->findBy(['session' => $session]);
            foreach ($formations as $formation) {
                $totalDepense += $this->budgetCalculator->calculateFormationTTC($formation);
            }
        }

        $budgetInitial = (float) $projet->getBudgetinitial();
        $seuilAlerte   = (float) $projet->getSeuilalerte();
        $balance       = $budgetInitial - $totalDepense;
        $pourcentage   = $this->budgetCalculator->calculateBudgetUsagePercentage($projet, $totalDepense);

        $appels      = $this->appelDeFondsRepository->findBy(['projet' => $projet]);
        $totalAppels = array_sum(array_map(fn($a) => (float) $a->getMontantdemande(), $appels));

        return [
            'project_id'             => $projet->getProjetid(),
            'name'                   => $projet->getNom(),
            'budget_initial'         => round($budgetInitial, 2),
            'total_spent'            => round($totalDepense, 2),
            'balance'                => round($balance, 2),
            'budget_used_percentage' => round($pourcentage, 2),
            'alert_threshold'        => round($seuilAlerte, 2),
            'alert_triggered'        => $this->budgetCalculator->isAlertThresholdReached($projet, $totalDepense),
            'total_appels_de_fonds'  => round($totalAppels, 2),
            'currency'               => 'EUR',
            'client'                 => [
                'id'   => $projet->getClient()->getClientid(),
                'name' => $projet->getClient()->getNom(),
            ],
            'referent'               => [
                'id'    => $projet->getReferent()->getUtilisateurid(),
                'name'  => $projet->getReferent()->getNom(),
                'email' => $projet->getReferent()->getEmail(),
            ],
        ];
    }

    private function buildAppelData(AppelDeFonds $appel): array
    {
        return [
            'appel_id'       => $appel->getAppelid(),
            'project_id'     => $appel->getProjet()->getProjetid(),
            'montantdemande' => round((float) $appel->getMontantdemande(), 2),
            'datedemande'    => $appel->getDatedemande()->format('Y-m-d'),
            'currency'       => 'EUR',
        ];
    }
}
