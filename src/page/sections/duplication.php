<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

$post_id = $CONFIG['post_id'];
$customFields = [];
$yoastFields = [];
if (isset($_POST['save']) && $_POST['save'] == "duplication") {
    $post_id = $_POST['post_id'] ?? $CONFIG['post_id'];
    if (isset($post_id)) {
        $CONFIG['post_id'] = $post_id;
        $respond_duplicados = [
            "status" => "ok",
            "message" => "Post Cargado.",
            'data' => [],
        ];
    }
    if (isset($post_id) && isset($_POST['set_custom_field']) && $_POST['set_custom_field'] == "1") {
        $customFields = $_POST['customFields'] ?? [];
        if (!empty($customFields)) {
            DPAI_CF::SET($post_id, $customFields);
            $respond_duplicados = [
                "status" => "ok",
                "message" => "Campos personalisados Guardados.",
                'data' => [],
            ];
        }
        $yoastFields = $_POST['yoastFields'] ?? [];
        if (!empty($yoastFields)) {
            DPAI_YOAST::SET($post_id, $yoastFields);
            $respond_duplicados = [
                "status" => "ok",
                "message" => "Campos personalisados Guardados.",
                'data' => [],
            ];
        }
    }
    if (isset($post_id) && isset($_POST['generate_duplicate']) && $_POST['generate_duplicate'] == "1") {
        $prompt = $_POST['prompt'];
        if (isset($prompt)) {
            $CONFIG['prompt'] = $prompt;
            $customFields = DPAI_CF::GET($post_id);
            $yoastFields = DPAI_YOAST::GET($post_id);
            $respond_duplicados = DPAI_DUPLICADOS::getDuplicados($post_id, $prompt, $customFields, $yoastFields);
            FWUSystemLog::add(DPAI_KEY, [
                'type' => "respond_duplicados",
                'data' => $respond_duplicados
            ]);
            if ($respond_duplicados['status'] == 'ok') {
                $POST_DATA = $DUPLICADOS[$post_id] ?? [];
                $POST_DATA['post_id'] = $post_id;
                $POST_DATA['customFields'] = $customFields;
                $POST_DATA['yoastFields'] = $yoastFields;
                $POST_DATA['variations'] ??= [];
                $POST_DATA['variations'][$prompt] = $respond_duplicados['data'];
                $DPAI_USE_DATA_DUPLICADOS->setField($post_id, $POST_DATA);
            }
        }
    }
    FWUSystemLog::add(DPAI_KEY, [
        'type' => "save_duplication",
        'data' => $_POST
    ]);
    $DPAI_USE_DATA_CONFIG->set($CONFIG);
}
if (isset($post_id)) {
    $customFields = DPAI_CF::GET($post_id);
    $yoastFields = DPAI_YOAST::GET($post_id);
}

?>
<form method="post">
    <?php
    if (isset($respond_duplicados)) {
        getRespond($respond_duplicados);
    }
    ?>
    <input type="hidden" name="save" value="duplication">
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="post_id">
                    Post
                    <?= tooltip('Selecciona la página a duplicar.') ?>
                </label>
            </th>
            <td>
                <?php
                wp_dropdown_pages([
                    'name'              => 'post_id',
                    'id'                => 'post_id',
                    'show_option_none'  => '-- Seleccionar --',
                    'option_none_value' => '',
                    'selected'          => $post_id,
                ]);
                ?>
            </td>
        </tr>
        <?php
        if (isset($post_id)) {
            $post = get_post_meta($post_id);
        ?>
            <tr>
                <th scope="row">
                    <label for="post_id">
                        Post Name
                        <?= tooltip('Nombre de la pagina a duplicar.') ?>
                    </label>
                </th>
                <td>
                    <?= get_the_title($post_id); ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="post_id">
                        Custom Fields
                        <?= tooltip('Campos personalizados de la pagina.') ?>
                    </label>
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <table class="form-table">
                        <?php
                        foreach ($customFields as $key => $value) {
                        ?>
                            <tr>
                                <th scope="row">
                                    <label for="<?= $key ?>">
                                        <?= $key ?>
                                    </label>
                                </th>
                                <td>
                                    <input
                                        type="text"
                                        id="<?= $key ?>"
                                        name="customFields[<?= $key ?>]"
                                        placeholder="<?= $key ?>"
                                        value="<?= esc_attr($value) ?>"
                                        class="regular-text" />
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </td>
            </tr>
            <?php
            if (function_exists('YoastSEO')) {
            ?>
                <tr>
                    <th scope="row">
                        <label for="post_id">
                            Yoast Seo
                            <?= tooltip('Campos que usa el plugin Yoast Seo.') ?>
                        </label>
                    </th>
                </tr>
                <tr>
                    <td colspan="2">
                        <table class="form-table">
                            <?php
                            foreach ($yoastFields as $key => $value) {
                            ?>
                                <tr>
                                    <th scope="row">
                                        <label for="<?= $key ?>">
                                            <?= $key ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input
                                            type="text"
                                            id="<?= $key ?>"
                                            name="yoastFields[<?= $key ?>]"
                                            placeholder="<?= $key ?>"
                                            value="<?= esc_attr($value) ?>"
                                            class="regular-text" />
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </td>
                </tr>
            <?php
            }
            ?>
        <?php
        }
        ?>
    </table>

    <div class="content-btn">
        <button
            type="submit"
            name="submit"
            value="Cargar Post"
            class="button button-primary">
            Cargar Post
        </button>

        <?php
        if (isset($post_id)) {
            $post = get_post_meta($post_id);
        ?>
            <button
                type="submit"
                name="set_custom_field"
                value="1"
                class="button delete">
                Guardar Campos Personalisados
            </button>
        <?php
        }
        ?>
    </div>
    <?php
    if (isset($post_id)) {
        $post = get_post_meta($post_id);
    ?>
        <h3>Prompt para generar Duplicados</h3>
        <textarea
            id="prompt"
            name="prompt"
            placeholder="Generar paginas duplicadas basandose en ...."
            class="large-text code"
            style="min-height: 200px;"
            rows="8"><?= $CONFIG['prompt'] ?></textarea>

        <button
            type="submit"
            name="generate_duplicate"
            value="1"
            class="button">
            Generar Duplicados
        </button>
    <?php
    }
    ?>

</form>
<?php
