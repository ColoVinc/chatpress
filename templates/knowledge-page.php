<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap jeenie-settings">

    <div class="jeenie-header rounded-3 mb-4 d-flex align-items-center gap-3 p-4">
        <h1 class="text-white m-0 fs-4"><i class="fa-solid fa-book"></i> <?php esc_html_e( 'Jeenie — Knowledge Base', 'jeenie' ); ?></h1>
    </div>

    <div class="row g-4">

        <!-- UPLOAD -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-upload"></i> <?php esc_html_e( 'Aggiungi Documento', 'jeenie' ); ?></h2>
                    <p class="text-muted small"><?php esc_html_e( 'Incolla il testo di un documento (FAQ, linee guida, listino, ecc.). L\'AI lo userà come contesto nelle risposte.', 'jeenie' ); ?></p>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?php esc_html_e( 'Nome documento', 'jeenie' ); ?></label>
                        <input type="text" id="jeenie-kb-name" class="form-control form-control-sm" placeholder="<?php esc_attr_e( 'es. FAQ Aziendali, Linee Guida Brand...', 'jeenie' ); ?>" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?php esc_html_e( 'Contenuto', 'jeenie' ); ?></label>
                        <textarea id="jeenie-kb-content" class="form-control form-control-sm" rows="10" placeholder="<?php esc_attr_e( 'Incolla qui il testo del documento...', 'jeenie' ); ?>"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold"><?php esc_html_e( 'Oppure carica un file .txt', 'jeenie' ); ?></label>
                        <input type="file" id="jeenie-kb-file" class="form-control form-control-sm" accept=".txt" />
                    </div>

                    <button type="button" id="jeenie-kb-upload" class="btn btn-primary btn-sm w-100">
                        <i class="fa-solid fa-plus"></i> <?php esc_html_e( 'Salva Documento', 'jeenie' ); ?>
                    </button>

                    <div id="jeenie-kb-result" class="mt-2 small" style="display:none;"></div>
                </div>
            </div>

            <!-- Impostazioni -->
            <div class="card mt-4">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-gear"></i> <?php esc_html_e( 'Impostazioni', 'jeenie' ); ?></h2>
                    <form method="post" action="options.php">
                        <?php settings_fields( 'jeenie_knowledge_settings' ); ?>
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e( 'Knowledge Base attiva', 'jeenie' ); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="jeenie_knowledge_enabled" value="1" <?php checked( get_option( 'jeenie_knowledge_enabled', 1 ) ); ?> />
                                        <?php esc_html_e( 'Usa la knowledge base come contesto nella chat', 'jeenie' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><?php esc_html_e( 'Limite contesto (caratteri)', 'jeenie' ); ?></th>
                                <td>
                                    <input type="number" name="jeenie_knowledge_max_chars" value="<?php echo esc_attr( get_option( 'jeenie_knowledge_max_chars', 1500 ) ); ?>" min="500" max="5000" class="small-text" />
                                    <p class="description"><?php esc_html_e( 'Massimo caratteri di knowledge base iniettati nel prompt. Più alto = più contesto ma più token.', 'jeenie' ); ?></p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button( __( 'Salva', 'jeenie' ), 'secondary', 'submit', false ); ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- LISTA DOCUMENTI -->
        <div class="col-md-7">
            <!-- RAG: indicizzazione post -->
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-database"></i> <?php esc_html_e( 'RAG — Indicizzazione Contenuti', 'jeenie' ); ?></h2>
                    <p class="text-muted small"><?php esc_html_e( 'Indicizza i post e le pagine del sito per permettere all\'AI di conoscere i tuoi contenuti esistenti.', 'jeenie' ); ?></p>
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" id="jeenie-index-posts" class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-arrows-rotate"></i> <?php esc_html_e( 'Indicizza tutti i post', 'jeenie' ); ?>
                        </button>
                        <span class="text-muted small">
                            <?php
                            $jeenie_indexed = Jeenie_Knowledge::count_indexed_posts();
                            // translators: %d is the number of indexed posts
                            echo esc_html( sprintf( __( '%d post attualmente indicizzati', 'jeenie' ), $jeenie_indexed ) );
                            ?>
                        </span>
                    </div>
                    <div id="jeenie-index-result" class="mt-2 small" style="display:none;"></div>
                    <p class="text-muted small mt-2 mb-0"><i class="fa-solid fa-circle-info"></i> <?php esc_html_e( 'I nuovi post vengono indicizzati automaticamente alla pubblicazione.', 'jeenie' ); ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title fs-6 pb-2 border-bottom"><i class="fa-solid fa-folder-open"></i> <?php esc_html_e( 'Documenti Caricati', 'jeenie' ); ?></h2>

                    <?php if ( empty( $documents ) ) : ?>
                        <p class="text-muted small"><?php esc_html_e( 'Nessun documento caricato. Aggiungi il primo dalla sezione a sinistra.', 'jeenie' ); ?></p>
                    <?php else : ?>
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><?php esc_html_e( 'Documento', 'jeenie' ); ?></th>
                                    <th><?php esc_html_e( 'Frammenti', 'jeenie' ); ?></th>
                                    <th><?php esc_html_e( 'Data', 'jeenie' ); ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="jeenie-kb-list">
                                <?php foreach ( $documents as $jeenie_doc ) : ?>
                                    <tr data-name="<?php echo esc_attr( $jeenie_doc['doc_name'] ); ?>">
                                        <td><i class="fa-solid fa-file-lines"></i> <?php echo esc_html( $jeenie_doc['doc_name'] ); ?></td>
                                        <td><?php echo esc_html( $jeenie_doc['chunks'] ); ?></td>
                                        <td><?php echo esc_html( $jeenie_doc['created_at'] ); ?></td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-sm jeenie-kb-delete" data-name="<?php echo esc_attr( $jeenie_doc['doc_name'] ); ?>">
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
