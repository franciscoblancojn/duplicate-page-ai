<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

$post_id = $_POST['post_id'];

if (isset($post_id) && isset($_POST['set_custom_field']) && $_POST['set_custom_field'] == "1") {
    $customFields = $_POST['customFields'] ?? [];
    if (!empty($customFields)) {
        $result = DPAI_WP_JSON::setCustomFields($post_id, $customFields);
    }
}

?>
<form method="post">
    <input type="hidden" name="save" value="2">
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="post_id">
                    Post id
                    <?= tooltip('Id de la pagina a duplicar.') ?>
                </label>
            </th>
            <td>
                <input
                    type="number"
                    id="post_id"
                    name="post_id"
                    placeholder="Post id"
                    value="<?= $post_id ?>"
                    class="regular-text" />
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
                    <?php
                    $customFields = DPAI_WP_JSON::getCustomFields($post_id);
                    ?>
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
        }
        ?>
    </table>

    <div class="content-btn">
        <?php submit_button('Ejecutar'); ?>

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
</form>
<?php
