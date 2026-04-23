<?php
// var_dump([
//     "is_user_admin" => current_user_can( 'manage_options' )
// ]);

if (isset($_POST['save']) && $_POST['save'] == "duplicates_pendding") {
    //PRUEBAS:
    if (isset($_POST['submit_test']) && $_POST['submit_test'] == 'submit_test') {
        // $DPAI_USE_DATA_DUPLICADOS->set(json_decode('{"73":{"post_id":"73","customFields":{"titulo_principal":"Automatiza tu log\u00edstica.","titulo_secundario":"Protege tu flujo de caja. Escala sin fricciones","descripccion_principal":"Aveonline pone al servicio de tu comercio electr\u00f3nico m\u00e1s de 12 a\u00f1os de experiencia, tecnolog\u00eda con inteligencia artificial y automatizaciones listas para usar. Olv\u00eddate de desarrollos costosos: desde la validaci\u00f3n de direcciones hasta el recaudo, todo funciona para ti desde el primer clic.","btn_register_text":"Activa tu cuenta \u2013 Gratis y sin riesgos"},"variations":{"Genera una lista de variaciones para Cucuta, Medellin y Bogota en Colombia":[{"title":"Test - Cucuta","customFields":{"titulo_principal":"Automatiza tu log\u00edstica en C\u00facuta.","titulo_secundario":"Protege tu flujo de caja. Escala sin fricciones.","descripccion_principal":"Aveonline pone al servicio de tu comercio electr\u00f3nico en C\u00facuta m\u00e1s de 12 a\u00f1os de experiencia, tecnolog\u00eda con inteligencia artificial y automatizaciones listas para usar. Olv\u00eddate de desarrollos costosos: desde la validaci\u00f3n de direcciones hasta el recaudo, todo funciona para ti desde el primer clic.","btn_register_text":"Activa tu cuenta en C\u00facuta \u2013 Gratis y sin riesgos"}},{"title":"Test - Medellin","customFields":{"titulo_principal":"Automatiza tu log\u00edstica en Medell\u00edn.","titulo_secundario":"Protege tu flujo de caja. Escala sin fricciones.","descripccion_principal":"Aveonline pone al servicio de tu comercio electr\u00f3nico en Medell\u00edn m\u00e1s de 12 a\u00f1os de experiencia, tecnolog\u00eda con inteligencia artificial y automatizaciones listas para usar. Olv\u00eddate de desarrollos costosos: desde la validaci\u00f3n de direcciones hasta el recaudo, todo funciona para ti desde el primer clic.","btn_register_text":"Activa tu cuenta en Medell\u00edn \u2013 Gratis y sin riesgos"}},{"title":"Test - Bogota","customFields":{"titulo_principal":"Automatiza tu log\u00edstica en Bogot\u00e1.","titulo_secundario":"Protege tu flujo de caja. Escala sin fricciones.","descripccion_principal":"Aveonline pone al servicio de tu comercio electr\u00f3nico en Bogot\u00e1 m\u00e1s de 12 a\u00f1os de experiencia, tecnolog\u00eda con inteligencia artificial y automatizaciones listas para usar. Olv\u00eddate de desarrollos costosos: desde la validaci\u00f3n de direcciones hasta el recaudo, todo funciona para ti desde el primer clic.","btn_register_text":"Activa tu cuenta en Bogot\u00e1 \u2013 Gratis y sin riesgos"}}]}}}',true));
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    //OK: ELIMINAR TODOS
    if (isset($_POST['submit_delete']) && $_POST['submit_delete'] == 'delete_all') {
        $DPAI_USE_DATA_DUPLICADOS->set([]);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    //OK: ELIMINAR UNA VARIAION
    if (isset($_POST['submit_delete']) && $_POST['submit_delete'] != 'generate_all') {
        [$post_id, $prompt, $v] = explode(DPAI_KEY_SEPARETE, $_POST['submit_delete']);
        $post_id = (int)$post_id;
        $v = (int)$v;
        $DPAI_USE_DATA_DUPLICADOS->deleteVariation($post_id, $prompt, $v);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    // PENDING: GENERAR TODOS
    if (isset($_POST['submit_generate']) && $_POST['submit_generate'] == 'generate_all') {
    }
    // OK: GENERAR UNO
    if (isset($_POST['submit_generate']) && $_POST['submit_generate'] != 'generate_all') {
        [$post_id, $prompt, $v] = explode(DPAI_KEY_SEPARETE, $_POST['submit_generate']);
        $post_id = (int)$post_id;
        $v = (int)$v;
        $respond_duplicates_pendding = $DPAI_USE_DATA_DUPLICADOS->generateVariation($post_id, $prompt, $v);
        $DUPLICADOS = $DPAI_USE_DATA_DUPLICADOS->get();
    }
}
?>
<form method="post">
    <input type="hidden" name="save" value="duplicates_pendding">
    <?php
    if (count($DUPLICADOS) == 0) {
    ?>
        <h3>
            No tienes duplicaciones de paginas pendientes.
        </h3>
    <?php
    } else {
    ?>
        <div class="content-title-btn">
            <h3>
                Lista de Duplicaciones de paginas.
            </h3>
            <div class="content-btn">
                <!-- <button
                    type="submit"
                    name="submit_test"
                    value="submit_test"
                    class="button button-primary">
                    Test
                </button> -->
                <button
                    type="submit"
                    name="submit_delete"
                    value="delete_all"
                    class="button button-primary">
                    Eliminar todos
                </button>
                <button
                    type="submit"
                    name="submit_generate"
                    value="generate_all"
                    class="button button-primary">
                    Generar todos
                </button>
            </div>
        </div>

    <?php
    }

    ?>
    <table class="form-table">
        <?php
        foreach ($DUPLICADOS as $post_id => $duplication) {
            $customFields = $duplication['customFields'];
            $variations = $duplication['variations'];
        ?>
            <tr>
                <th scope="row">
                    <label>
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
                    <label>
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
                                        name="duplication[<?= $post_id ?>][customFields][<?= $key ?>]"
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
            <tr>
                <th scope="row">
                    <label>
                        Variaciones
                        <?= tooltip('Variacion de pagina a generar.') ?>
                    </label>
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <table class="form-table">
                        <?php
                        foreach ($variations as $prompt => $variation) {
                        ?>
                            <tr>
                                <th scope="row">
                                    <label>
                                        Prompt
                                    </label>
                                </th>
                                <td>
                                    <?= $prompt ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table class="form-table">
                                        <?php
                                        foreach ($variation as $v => $value) {
                                            $customFields = $value['customFields'];
                                        ?>
                                            <tr>
                                                <th scope="row">
                                                    <label>
                                                        <?= $value['title'] ?>
                                                    </label>
                                                </th>
                                                <td>
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
                                                                        name="duplication[<?= $post_id ?>][variations][<?= $v ?>][customFields][<?= $key ?>]"
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
                                                <td>
                                                    <?php
                                                    $url = add_query_arg($customFields, get_permalink($post_id));
                                                    ?>

                                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="button delete">
                                                        Previsualizar
                                                    </a>
                                                </td>
                                                <td>
                                                    <button
                                                        type="submit"
                                                        name="submit_delete"
                                                        value="<?= $post_id . DPAI_KEY_SEPARETE . $prompt . DPAI_KEY_SEPARETE . $v ?>"
                                                        class="button button-primary">
                                                        Eliminar
                                                    </button>
                                                </td>
                                                <td>

                                                    <button
                                                        type="submit"
                                                        name="submit_generate"
                                                        value="<?= $post_id . DPAI_KEY_SEPARETE . $prompt . DPAI_KEY_SEPARETE . $v ?>"
                                                        class="button button-primary">
                                                        Generar
                                                    </button>
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
                    </table>
                </td>
            </tr>
        <?php
        }
        ?>

    </table>

    <?php
    if (isset($respond_duplicates_pendding)) {
    ?>
        <p class="message <?= $respond_duplicates_pendding['status'] ?>" data="<?= json_encode($respond_duplicates_pendding['data']) ?>">
            <?= parseRespondMessage($respond_duplicates_pendding['message']); ?>
            <?php
            if ($respond_duplicates_pendding['status'] == "ok") {
                $data = $respond_duplicates_pendding['data'];
                if (isset($data['url'])) {
            ?>
                    <a href="<?php echo esc_url($data['url']); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary btn-to-right">
                        Ver Pagina
                    </a>
            <?php
                }
            }
            ?>
        </p>
    <?php
    }
    ?>
</form>