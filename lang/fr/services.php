<?php

return [
    'services' => 'Services',
    'product' => 'Produit',
    'price' => 'Prix ',
    'status' => 'État ',
    'name' => 'Nom ',
    'actions' => 'Actions',
    'view' => 'Afficher',

    'product_details' => 'Informations sur le produit ',
    'billing_cycle' => 'Cycle de facturation ',
    'cancel' => 'Résilier ce service',
    'cancellation' => 'Résiliation de :service',
    'cancel_are_you_sure' => 'Êtes-vous sûr de vouloir résilier ce service ?',
    'cancel_reason' => 'Motif de la résiliation',
    'cancel_type' => 'Type de résiliation',
    'cancel_immediate' => 'Immédiate',
    'cancel_end_of_period' => 'À l\'échéance du service',
    'cancel_immediate_warning' => 'Si vous choisissez de continuer, le service sera résilié immédiatement sans remboursement et vous ne pourrez plus l\'utiliser.',
    'cancellation_requested' => 'Résiliation demandée',

    'current_plan' => 'Offre actuelle',
    'new_plan' => 'Nouvelle offre',
    'change_plan' => 'Changer d\'offre',
    'current_price' => 'Prix actuel',
    'new_price' => 'Nouveau prix',
    'upgrade' => 'Mettre à niveau',
    'upgrade_summary' => 'Résumé de la mise à niveau',
    'total_today' => 'Total à régler ce jour',
    'upgrade_service' => 'Mise à niveau du service',
    'upgrade_choose_product' => 'Choisissez un produit vers lequel mettre à niveau',
    'upgrade_choose_config' => 'Choisissez une configuration pour la mise à niveau',
    'next_step' => 'Étape suivante',

    'upgrade_pending' => 'Vous ne pouvez pas effectuer de mise à niveau tant qu\'une facture de mise à niveau ou de rétrogradation est en cours',

    'outstanding_invoice' => 'Vous avez une facture impayée.',
    'view_and_pay' => 'Afficher la facture et payer',

    'statuses' => [
        'pending' => 'En attente',
        'active' => 'Actif',
        'cancelled' => 'Terminé',
        'suspended' => 'Suspendu',
        'cancellation_pending' => 'Résiliation en attente',
    ],
    'billing_cycles' => [
        'day' => 'jour|jours',
        'week' => 'semaine|semaines',
        'month' => 'mois|mois',
        'year' => 'ans|ans',
    ],
    'every_period' => 'Tous les :period :unit',
    'price_every_period' => ':price tous les :period :unit',
    'price_one_time' => ':price au premier règlement',
    'expires_at' => 'Expire le ',
    'auto_pay' => 'Renouvellement automatique en utilisant',
    'auto_pay_not_configured' => '(non configuré)',

    'no_services' => 'Vous n\'avez à ce jour aucun service.',
    'update_billing_agreement' => 'Mettre à jour l\'autorisation de prélèvement',
    'clear_billing_agreement' => 'Effacer l\'autorisation de prélèvement',
    'select_billing_agreement' => 'Sélectionner l\'autorisation de prélèvement',

    'remove_payment_method' => 'Supprimer le mode de paiement',
    'remove_payment_method_confirm' => 'Êtes-vous sûr de vouloir retirer le mode de paiement ":name" pour ce service ? Vous ne serez plus prélevé automatiquement pour ce service uniquement.',
];
