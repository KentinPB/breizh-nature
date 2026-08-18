<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 1. Ajouter un sous-menu pour gérer les réservations
 */
function bnr_add_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=activite', // Se place sous le menu du CPT "Activités"
        'Gestion des réservations',    // Titre de la page web
        'Réservations',                // Titre dans le menu de gauche
        'manage_reservations',              // Droits requis
        'bnr-reservations',            // Slug de l'URL
        'bnr_render_admin_page'        // Fonction d'affichage
    );
}
add_action( 'admin_menu', 'bnr_add_admin_menu' );

/**
 * 2. Affichage de la page et traitement des mises à jour de statut
 */
function bnr_render_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservations'; // S'adapte automatiquement à votre préfixe (ex: bn_reservations)

    // Étape A : Traitement du formulaire si l'administrateur modifie un statut
    if ( isset( $_POST['action'] ) && $_POST['action'] === 'update_statut' && isset( $_POST['bnr_admin_nonce'] ) ) {
        // Sécurité exigée par le client : vérification du nonce[cite: 3]
        if ( wp_verify_nonce( $_POST['bnr_admin_nonce'], 'update_statut_action' ) ) {
            $resa_id = intval( $_POST['resa_id'] );
            $nouveau_statut = sanitize_text_field( $_POST['nouveau_statut'] );

            // On s'assure que la valeur correspond aux statuts autorisés[cite: 3]
            if ( in_array( $nouveau_statut, array( 'En attente', 'Acceptée', 'Refusée' ) ) ) {
                $wpdb->update(
                    $table_name,
                    array( 'statut' => $nouveau_statut ), // Ce qu'on met à jour
                    array( 'id' => $resa_id ),            // La condition (WHERE id = X)
                    array( '%s' ),
                    array( '%d' )
                );
                echo '<div class="notice notice-success is-dismissible"><p>Statut mis à jour avec succès.</p></div>';
            }
        }
    }

    // Étape B : Récupération de toutes les réservations en base de données
    $reservations = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY date_creation DESC" );
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Gestion des Réservations</h1>
        <p>Retrouvez ici toutes les demandes soumises par les visiteurs pour vos activités.</p>

        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Date de demande</th>
                <th>Client</th>
                <th>Contact</th>
                <th>Activité (ID)</th>
                <th>Nb.</th>
                <th>Commentaire</th>
                <th>Statut actuel</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if ( $reservations ) : ?>
                <?php foreach ( $reservations as $resa ) : ?>
                    <tr>
                        <td>#<?php echo esc_html( $resa->id ); ?></td>
                        <td><?php echo esc_html( date( 'd/m/Y H:i', strtotime( $resa->date_creation ) ) ); ?></td>
                        <td><strong><?php echo esc_html( $resa->prenom . ' ' . $resa->nom ); ?></strong></td>
                        <td>
                            <a href="mailto:<?php echo esc_attr( $resa->email ); ?>"><?php echo esc_html( $resa->email ); ?></a><br>
                            <?php echo esc_html( $resa->telephone ); ?>
                        </td>
                        <td><?php echo esc_html( $resa->activite_id ); ?></td>
                        <td><?php echo esc_html( $resa->participants ); ?> pers.</td>
                        <td><?php echo esc_html( $resa->commentaire ); ?></td>
                        <td>
                            <strong>
                                <?php
                                // Code couleur dynamique pour faciliter la lecture du gestionnaire
                                if( $resa->statut === 'Acceptée' ) echo '<span style="color:green;">' . esc_html($resa->statut) . '</span>';
                                elseif( $resa->statut === 'Refusée' ) echo '<span style="color:red;">' . esc_html($resa->statut) . '</span>';
                                else echo '<span style="color:orange;">' . esc_html($resa->statut) . '</span>';
                                ?>
                            </strong>
                        </td>
                        <td>
                            <!-- Formulaire permettant de modifier le statut[cite: 3] -->
                            <form method="POST" style="display:flex; gap:5px; align-items:center;">
                                <?php wp_nonce_field( 'update_statut_action', 'bnr_admin_nonce' ); ?>
                                <input type="hidden" name="action" value="update_statut">
                                <input type="hidden" name="resa_id" value="<?php echo esc_attr( $resa->id ); ?>">
                                <select name="nouveau_statut">
                                    <option value="En attente" <?php selected( $resa->statut, 'En attente' ); ?>>En attente</option>
                                    <option value="Acceptée" <?php selected( $resa->statut, 'Acceptée' ); ?>>Acceptée</option>
                                    <option value="Refusée" <?php selected( $resa->statut, 'Refusée' ); ?>>Refusée</option>
                                </select>
                                <button type="submit" class="button button-small">OK</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="9">Aucune réservation pour le moment.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}