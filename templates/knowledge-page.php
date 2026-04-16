<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap sitegenie-settings">

    <div class="sitegenie-header rounded-3 mb-4 d-flex align-items-center gap-3 p-4">
        <h1 class="text-white m-0 fs-4"><i class="fa-solid fa-book"></i> <?php esc_html_e( 'SiteGenie — Knowledge Base', 'sitegenie' ); ?></h1>
    </div>

    <div class="row g-4">

        <!-- UPLOAD -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-upload"></i> <?php esc_html_e( 'Aggiungi Documento', 'sitegenie' ); ?></h2>
                    <p class="text-muted small"><?php esc_html_e( 'Incolla il testo di un documento (FAQ, linee guida, listino, ecc.). L\'AI lo userà come contesto nelle risposte.', 'sitegenie' ); ?></p>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?php esc_html_e( 'Nome documento', 'sitegenie' ); ?></label>
                        <input type="text" id="sitegenie-kb-name" class="form-control form-control-sm" placeholder="<?php esc_attr_e( 'es. FAQ Aziendali, Linee Guida Brand...', 'sitegenie' ); ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?php esc_html_e( 'Contenuto', 'sitegenie' ); ?></label>
                        <textarea id="sitegenie-kb-content" class="form-control form-control-sm" rows="10" placeholder="<?php esc_attr_e( 'Incolla qui il testo del documento...', 'sitegenie' ); ?>"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?php esc_html_e( 'Oppure carica un file .txt', 'sitegenie' ); ?></label>
                        <input type="file" id="sitegenie-kb-file" class="form-control form-control-sm" accept=".txt" />
                    </div>

                    <button type="button" id="sitegenie-kb-upload" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-plus"></i> <?php esc_html_e( 'Salva Documento', 'sitegenie' ); ?>
                    </button>

                    <div id="sitegenie-kb-result" class="mt-2 small" style="display:none;"></div>
                </div>
            </div>

            <!-- Impostazioni -->
            <div class="card mt-4">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-gear"></i> <?php esc_html_e( 'Impostazioni', 'sitegenie' ); ?></h2>
                    <form method="post" action="options.php">
                        <?php settings_fields( 'sitegenie_knowledge_settings' ); ?>
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e( 'Knowledge Base attiva', 'sitegenie' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="sitegenie_knowledge_enabled" value="1" <?php checked( get_option( 'sitegenie_knowledge_enabled', 1 ) ); ?> />
                                        <?php esc_html_e( 'Usa la knowledge base come contesto nella chat', 'sitegenie' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e( 'Limite contesto (caratteri)', 'sitegenie' ); ?></th>
                                <td>
                                    <input type="number" name="sitegenie_knowledge_max_chars" value="<?php echo esc_attr( get_option( 'sitegenie_knowledge_max_chars', 1500 ) ); ?>" min="500" max="5000" class="small-text" />
                                    <p class="description"><?php esc_html_e( 'Massimo caratteri di knowledge base iniettati nel prompt. Più alto = più contesto ma più token.', 'sitegenie' ); ?></p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button( __( 'Salva', 'sitegenie' ), 'secondary', 'submit', false ); ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- LISTA DOCUMENTI -->
        <div class="col-md-7">
            <!-- RAG: indicizzazione post -->
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-database"></i> <?php esc_html_e( 'RAG — Indicizzazione Contenuti', 'sitegenie' ); ?></h2>
                    <p class="text-muted small"><?php esc_html_e( 'Indicizza i post e le pagine del sito per permettere all\'AI di conoscere i tuoi contenuti esistenti.', 'sitegenie' ); ?></p>
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" id="sitegenie-index-posts" class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-arrows-rotate"></i> <?php esc_html_e( 'Indicizza tutti i post', 'sitegenie' ); ?>
                        </button>
                        <span class="text-muted small">
                            <?php
                            $indexed = SiteGenie_Knowledge::count_indexed_posts();
                            // translators: %d is the number of indexed posts
                            echo esc_html( sprintf( __( '%d post attualmente indicizzati', 'sitegenie' ), $indexed ) );
                            ?>
                        </span>
                    </div>
                    <div id="sitegenie-index-result" class="mt-2 small" style="display:none;"></div>
                    <p class="text-muted small mt-2 mb-0"><i class="fa-solid fa-circle-info"></i> <?php esc_html_e( 'I nuovi post vengono indicizzati automaticamente alla pubblicazione.', 'sitegenie' ); ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-folder-open"></i> <?php esc_html_e( 'Documenti Caricati', 'sitegenie' ); ?></h2>

                    <?php if ( empty( $documents ) ) : ?>
                        <p class="text-muted small"><?php esc_html_e( 'Nessun documento caricato. Aggiungi il primo dalla sezione a sinistra.', 'sitegenie' ); ?></p>
                    <?php else : ?>
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><?php esc_html_e( 'Documento', 'sitegenie' ); ?></th>
                                    <th><?php esc_html_e( 'Frammenti', 'sitegenie' ); ?></th>
                                    <th><?php esc_html_e( 'Data', 'sitegenie' ); ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="sitegenie-kb-list">
                                <?php foreach ( $documents as $doc ) : ?>
                                    <tr data-name="<?php echo esc_attr( $doc['doc_name'] ); ?>">
                                        <td><i class="fa-solid fa-file-lines"></i> <?php echo esc_html( $doc['doc_name'] ); ?></td>
                                        <td><?php echo esc_html( $doc['chunks'] ); ?></td>
                                        <td><?php echo esc_html( $doc['created_at'] ); ?></td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm sitegenie-kb-delete" data-name="<?php echo esc_attr( $doc['doc_name'] ); ?>">
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
