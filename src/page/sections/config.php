<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;


if (isset($_POST['save']) && $_POST['save'] == "config") {
    if (isset($_POST['apikey'])) {
        $CONFIG['apikey'] = $_POST['apikey'];
    }
    FWUSystemLog::add(DPAI_KEY, [
        'type' => "save_config",
        'data' => $_POST
    ]);
    update_option(DPAI_CONFIG, $CONFIG);
}
?>
<form method="post">
    <input type="hidden" name="save" value="config">
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="apikey">
                    API KEY
                    <?= tooltip('Api key de Gemini para generar contenido con IA.') ?>
                </label>
            </th>
            <td>
                <input
                    type="text"
                    id="apikey"
                    name="apikey"
                    placeholder="API KEY"
                    value="<?= $CONFIG['apikey'] ?>"
                    class="regular-text" />
            </td>
        </tr>
    </table>

    <div class="content-btn">
        <?php submit_button('Guardar'); ?>
    </div>
</form>
<style>
    .content-btn {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
        margin-top: 2rem;
    }

    .content-btn .submit {
        margin: 0;
        padding: 0;
    }

    .goshap-tooltip {
        position: relative;
        cursor: pointer;
        margin-left: 6px;
        display: inline-block;
    }

    .goshap-tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 10px;
        border-width: 5px;
        border-style: solid;
        border-color: #1d2327 transparent transparent transparent;
    }

    .goshap-tooltip-text {
        visibility: hidden;
        opacity: 0;
        width: 360px;
        background: #1d2327;
        color: #fff;
        text-align: left;
        padding: 8px;
        border-radius: 6px;
        position: absolute;
        z-index: 9999;
        bottom: 125%;
        left: 0;
        transition: opacity 0.2s ease;
        font-size: 12px;
        line-height: 1.4;
    }

    .goshap-tooltip:hover .goshap-tooltip-text {
        visibility: visible;
        opacity: 1;
    }




    /* Contenedor general */
    details {
        margin-bottom: 1rem;
        border: 1px solid #dcdcde;
        border-radius: 8px;
        background: #fff;
        overflow: hidden;
    }

    /* Header tipo collapse */
    details summary {
        cursor: pointer;
        padding: 12px 16px;
        font-weight: 600;
        font-size: 14px;
        background: #f6f7f7;
        list-style: none;
        position: relative;
        transition: background 0.2s ease;
    }

    /* Hover */
    details summary:hover {
        background: #e5e5e5;
    }

    /* Quitar flecha default */
    details summary::-webkit-details-marker {
        display: none;
    }

    /* Flecha custom */
    details summary::after {
        content: "▸";
        position: absolute;
        right: 16px;
        font-size: 14px;
        transition: transform 0.2s ease;
    }

    /* Rotar cuando está abierto */
    details[open] summary::after {
        transform: rotate(90deg);
    }

    /* Contenido interno */
    details>div {
        padding: 16px;
        background: #ffffff;
        border-top: 1px solid #dcdcde;
        max-height: 75dvh;
        overflow: auto;
    }
</style>
<?php
