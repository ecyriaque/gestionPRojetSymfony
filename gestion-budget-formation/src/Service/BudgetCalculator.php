<?php

namespace App\Service;

use App\Entity\Projet;
use App\Entity\Formation;
use App\Entity\SessionFormation;

class BudgetCalculator
{
    /**
     * Calcule le montant total TTC d'une formation
     */
    public function calculateFormationTTC(Formation $formation): float
    {
        $ht = (float) $formation->getCouht();
        $tva = (float) $formation->getTauxtva();
        
        return $ht * (1 + ($tva / 100));
    }
    
    /**
     * Calcule le coût total d'une session de formation
     */
    public function calculateSessionTotal(SessionFormation $session, array $formations = null): float
    {
        $total = 0;
        
        // Si on fournit des formations spécifiques
        if ($formations !== null) {
            foreach ($formations as $formation) {
                if ($formation->getSession()->getSessionid() === $session->getSessionid()) {
                    $total += $this->calculateFormationTTC($formation);
                }
            }
            return $total;
        }
        
        // Sinon on retourne le total déjà enregistré
        return (float) $session->getCouttotal();
    }
    
    /**
     * Calcule le pourcentage de budget utilisé pour un projet
     */
    public function calculateBudgetUsagePercentage(Projet $projet, float $totalUtilise): float
    {
        $budgetInitial = (float) $projet->getBudgetinitial();
        
        if ($budgetInitial <= 0) {
            return 0;
        }
        
        return ($totalUtilise / $budgetInitial) * 100;
    }
    
    /**
     * Vérifie si le seuil d'alerte est atteint
     */
    public function isAlertThresholdReached(Projet $projet, float $totalUtilise): bool
    {
        $seuilAlerte = (float) $projet->getSeuilalerte();
        
        return $totalUtilise >= $seuilAlerte;
    }
} 