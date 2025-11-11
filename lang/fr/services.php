<?php

return [
    'services' => 'Services',
    'product' => 'Produit',
    'price' => 'Prix',
    'status' => 'État',
    'name' => 'Nom',
    'actions' => 'Actions',
    'view' => 'Afficher',

    'product_details' => 'Détails du produit',
    'billing_cycle' => 'Cycle de facturation',
    'cancel' => 'Annuler',
    'cancellation' => 'Annulation de :service',
    'cancel_are_you_sure' => 'Êtes-vous sûr de vouloir annuler ce service ?',
    'cancel_reason' => 'Raison de l\'annulation',
    'cancel_type' => 'Type d’annulation',
    'cancel_immediate' => 'Annuler immédiatement',
    'cancel_end_of_period' => 'Annuler à la fin de la période de facturation',
    'cancel_immediate_warning' => 'Lorsque vous appuyez sur le bouton ci-dessous, le service sera annulé immédiatement et vous ne pourrez plus l\'utiliser.',
    'cancellation_requested' => 'Annulation demandée',

    'current_plan' => 'Offre actuelle',
    'new_plan' => 'Nouvelle offre',
    'change_plan' => 'Changer d\'offre',
    'current_price' => 'Prix actuel',
    'new_price' => 'Nouveau prix',
    'upgrade' => 'Mettre à niveau',
    'upgrade_summary' => 'Résumé de la mise à niveau',
    'total_today' => 'Total aujourd\'hui',
    'upgrade_service' => 'Mise à niveau du Service',
    'upgrade_choose_product' => 'Choisissez un produit vers lequel mettre à niveau',
    'upgrade_choose_config' => 'Choisissez la configuration pour la mise à jour',
    'next_step' => 'Étape suivante',

    'upgrade_pending' => 'Vous ne pouvez pas effectuer de mise à niveau tant qu\'une facture de mise à niveau ou de rétrogradation est en cours',

    'outstanding_invoice' => 'Vous avez une facture impayée.',
    'view_and_pay' => 'Cliquez ici pour afficher et payer',

    'statuses' => [
        'pending' => 'En attente',
        'active' => 'Actif',
        'cancelled' => 'Annulée',
        'suspended' => 'Suspendue',
        'cancellation_pending' => 'Annulation en attente',
    ],
    'billing_cycles' => [
        'day' => 'jour|jours',
        'week' => 'semaine|semaines',
        'month' => 'mois|mois',
        'year' => 'an|ans',
    ],
    'every_period' => 'Chaque :period :unit',
    'price_every_period' => ':price pour :period :unit',
    'price_one_time' => ':price paiement unique',
    'expires_at' => 'Expire le',
    'auto_pay' => 'Paiement automatique en utilisant',
    'auto_pay_not_configured' => 'Non configuré',

    'no_services' => 'Aucun service trouvé',
    'update_billing_agreement' => 'Mettre à jour l\'accord de facturation',
    'clear_billing_agreement' => 'Effacer l\'accord de facturation',
    'select_billing_agreement' => 'Sélectionner l\'accord de facturation',

    'remove_payment_method' => 'Supprimer le moyen de paiement',
    'remove_payment_method_confirm' => 'Êtes-vous sûr de vouloir supprimer le mode de paiement ":name" de ce service ? Vous ne serez plus automatiquement prélevé pour ce service.',
];
