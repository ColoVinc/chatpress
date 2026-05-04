<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap vcai-settings">

    <div class="vcai-header rounded-3 mb-4 d-flex align-items-center gap-3 p-4">
        <h1 class="text-white m-0 fs-4"><i class="fa-solid fa-book"></i> <?php esc_html_e( 'VColonna AI — Knowledge Base', 'vc-colonna-ai-assistant' ); ?></h1>
    </div>

    <div class="row g-4">

        <!-- UPLOAD -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-upload"></i> <?php esc_html_e( 'Aggiungi Documento', 'vc-colonna-ai-assistant' ); ?></h2>
                    <p class="text-muted small"><?php esc_html_e( 'Incolla il testo di un documento (FAQ, linee guida, listino, ecc.). L\'AI lo userà come contesto nelle risposte.', 'vc-colonna-ai-assistant' ); ?></p>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?php esc_html_e( 'Nome documento', 'vc-colonna-ai-assistant' ); ?></label>
                        <input type="text" id="vcai-kb-name" class="form-control form-control-sm" placeholder="<?php esc_attr_e( 'es. FAQ Aziendali, Linee Guida Brand...', 'vc-colonna-ai-assistant' ); ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?php esc_html_e( 'Contenuto', 'vc-colonna-ai-assistant' ); ?></label>
                        <textarea id="vcai-kb-content" class="form-control form-control-sm" rows="10" placeholder="<?php esc_attr_e( 'Incolla qui il testo del documento...', 'vc-colonna-ai-assistant' ); ?>"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?php esc_html_e( 'Oppure carica un file .txt', 'vc-colonna-ai-assistant' ); ?></label>
                        <input type="file" id="vcai-kb-file" class="form-control form-control-sm" accept=".txt" />
                    </div>

                    <button type="button" id="vcai-kb-upload" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-plus"></i> <?php esc_html_e( 'Salva Documento', 'vc-colonna-ai-assistant' ); ?>
                    </button>

                    <div id="vcai-kb-result" class="mt-2 small" style="display:none;"></div>
                </div>
            </div>

            <!-- Impostazioni -->
            <div class="card mt-4">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-gear"></i> <?php esc_html_e( 'Impostazioni', 'vc-colonna-ai-assistant' ); ?></h2>
                    <form method="post" action="options.php">
                        <?php settings_fields( 'vcai_knowledge_settings' ); ?>
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e( 'Knowledge Base attiva', 'vc-colonna-ai-assistant' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="vcai_knowledge_enabled" value="1" <?php checked( get_option( 'vcai_knowledge_enabled', 1 ) ); ?> />
                                        <?php esc_html_e( 'Usa la knowledge base come contesto nella chat', 'vc-colonna-ai-assistant' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e( 'Limite contesto (caratteri)', 'vc-colonna-ai-assistant' ); ?></th>
                                <td>
                                    <input type="number" name="vcai_knowledge_max_chars" value="<?php echo esc_attr( get_option( 'vcai_knowledge_max_chars', 1500 ) ); ?>" min="500" max="5000" class="small-text" />
                                    <p class="description"><?php esc_html_e( 'Massimo caratteri di knowledge base iniettati nel prompt. Più alto = più contesto ma più token.', 'vc-colonna-ai-assistant' ); ?></p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button( __( 'Salva', 'vc-colonna-ai-assistant' ), 'secondary', 'submit', false ); ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- LISTA DOCUMENTI -->
        <div class="col-md-7">
            <!-- RAG: indicizzazione post -->
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-database"></i> <?php esc_html_e( 'RAG — Indicizzazione Contenuti', 'vc-colonna-ai-assistant' ); ?></h2>
                    <p class="text-muted small"><?php esc_html_e( 'Indicizza i post e le pagine del sito per permettere all\'AI di conoscere i tuoi contenuti esistenti.', 'vc-colonna-ai-assistant' ); ?></p>
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" id="vcai-index-posts" class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-arrows-rotate"></i> <?php esc_html_e( 'Indicizza tutti i post', 'vc-colonna-ai-assistant' ); ?>
                        </button>
                        <span class="text-muted small">
                            <?php
                            $vcai_indexed = Vcai_Knowledge::count_indexed_posts();
                            // translators: %d is the number of indexed posts
                            echo esc_html( sprintf( __( '%d post attualmente indicizzati', 'vc-colonna-ai-assistant' ), $vcai_indexed ) );
                            ?>
                        </span>
                    </div>
                    <div id="vcai-index-result" class="mt-2 small" style="display:none;"></div>
                    <p class="text-muted small mt-2 mb-0"><i class="fa-solid fa-circle-info"></i> <?php esc_html_e( 'I nuovi post vengono indicizzati automaticamente alla pubblicazione.', 'vc-colonna-ai-assistant' ); ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-folder-open"></i> <?php esc_html_e( 'Documenti Caricati', 'vc-colonna-ai-assistant' ); ?></h2>

                    <?php if ( empty( $documents ) ) : ?>
                        <p class="text-muted small"><?php esc_html_e( 'Nessun documento caricato. Aggiungi il primo dalla sezione a sinistra.', 'vc-colonna-ai-assistant' ); ?></p>
                    <?php else : ?>
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><?php esc_html_e( 'Documento', 'vc-colonna-ai-assistant' ); ?></th>
                                    <th><?php esc_html_e( 'Frammenti', 'vc-colonna-ai-assistant' ); ?></th>
                                    <th><?php esc_html_e( 'Data', 'vc-colonna-ai-assistant' ); ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="vcai-kb-list">
                                <?php foreach ( $documents as $vcai_doc ) : ?>
                                    <tr data-name="<?php echo esc_attr( $vcai_doc['doc_name'] ); ?>">
                                        <td><i class="fa-solid fa-file-lines"></i> <?php echo esc_html( $vcai_doc['doc_name'] ); ?></td>
                                        <td><?php echo esc_html( $vcai_doc['chunks'] ); ?></td>
                                        <td><?php echo esc_html( $vcai_doc['created_at'] ); ?></td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm vcai-kb-delete" data-name="<?php echo esc_attr( $vcai_doc['doc_name'] ); ?>">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
