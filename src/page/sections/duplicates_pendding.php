<?php
// var_dump([
//     "is_user_admin" => current_user_can( 'manage_options' )
// ]);

if (isset($_POST['save']) && $_POST['save'] == "duplicates_pendding") {
    if (isset($_POST['submit']) && $_POST['submit'] = 'delete_all') {
        $DPAI_USE_DATA_DUPLICADOS->set([]);
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    if (isset($_POST['submit']) && $_POST['submit'] = 'generate_all') {
        //PENDING:
    }
}

if (count($DUPLICADOS) == 0) {
?>
    <h3>
        No tienes duplicaciones de paginas pendientes.
    </h3>
<?php
} else {

?>
    <form method="post">
        <input type="hidden" name="save" value="duplicates_pendding">
        <div class="content-title-btn">
            <h3>
            Lista de Duplicaciones de paginas.
        </h3>
        <div class="content-btn">
            <button
                type="submit"
                name="submit"
                value="delete_all"
                class="button button-primary">
                Eliminar todos
            </button>
            <button
                type="submit"
                name="submit"
                value="generate_all"
                class="button button-primary">
                Generar todos
            </button>
        </div>
        </div>

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
                            foreach ($variations as $key => $variation) {
                            ?>
                                <tr>
                                    <th scope="row">
                                        <label>
                                            Prompt
                                        </label>
                                    </th>
                                    <td>
                                        <?= $key ?>
                                        <?= json_encode($value) ?>
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
                                                            name="submit"
                                                            value="generar"
                                                            class="button button-primary">
                                                            Eliminar
                                                        </button>
                                                    </td>
                                                    <td>

                                                        <button
                                                            type="submit"
                                                            name="submit"
                                                            value="generar"
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
    </form>

<?php
}

?>