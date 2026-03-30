<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap chatpress-settings">
    <div class="chatpress-header">
        <h1>Chatpress - Log chiamate</h1>
    </div>

    <div class="chatpress-stats-row">
        <div class="chatpress-stat-card">
            <span class="chatpress-stat-number"><?php echo intval( $stats['total_calls'] ) ?></span>
            <span class="chatpress-stat-label">Chiamate Totali</span>
        </div>
        <div class="chatpress-stat-card">
            <span class="chatpress-stat-number"><?php echo number_format( intval( $stats['total_tokens'] ) ); ?></span>
            <span class="chatpress-stat-label">Token usati</span>
        </div>
        <div class="chatpress-stat-card">
            <span class="chatpress-stat-number"><?php echo intval( $stats['total_errors'] ); ?></span>
            <span class="chapress-stat-label">Errori</span>
        </div>
    </div>

    <?php if ( empty( $logs ) ) : ?>
        <div class="chatpress-card">
            <p>Nessuna chiamata registrata ancora. Inizia ad usare ChatPress per vedere i log qui.</p>
        </div>
    <?php else : ?>
        <div class="chatpress-card">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Provider</th>
                        <th>Prompt Token</th>
                        <th>Completion Token</th>
                        <th>Totale</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $logs as $log ) : ?>
                        <tr>
                            <td><?php echo esc_html( $log['created_at'] ); ?></td>
                            <td><strong><?php echo esc_html( ucfirst( $log['provider'] ) ); ?></strong></td>
                            <td><?php echo intval( $log['prompt_tokens'] ); ?></td>
                            <td><?php echo intval( $log['completion_tokens'] ); ?></td>
                            <td><?php echo intval( $log['prompt_tokens']) + intval( $log['completion_tokens'] ); ?></td>
                            <td>
                                <?php if ( $log['status'] === 'success' ) : ?>
                                    <span class="chatpress-badge chatpress-badge--success">OK</span>
                                <?php else : ?>
                                    <span class="chatpress-badge chatpress-badge--error" title="<?php echo esc_attr( $log['error_message'] ); ?>">Errore</span>
                                <?php endif; ?>        
                            </td>
                        </tr>
                    <?php endforeach; ?>    
                </tbody>
            </table>
        </div>    
    <?php endif; ?>    
</div>