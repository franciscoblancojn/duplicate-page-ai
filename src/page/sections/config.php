<?php

use franciscoblancojn\wordpress_utils\FWUSystemLog;

function tooltip($text)
{
    return '
    <span class="goshap-tooltip">
        <span class="dashicons dashicons-info"></span>
        <span class="goshap-tooltip-text">' . $text . '</span>
    </span>';
}
if (isset($_POST['save']) && $_POST['save'] == 1) {
    $connections = $_POST['connections'] ?? [];
    if (isset($_POST['delete_connection'])) {
        $delete_connection = (int)$_POST['delete_connection'];

        // Eliminar la conexión
        if (isset($connections[$delete_connection])) {
            unset($connections[$delete_connection]);
        }

        // 🔥 Reindexar (MUY IMPORTANTE)
        $connections = array_values($connections);
    }
    for ($i = 0; $i < count($connections); $i++) {
        if (isset($connections[$i]['AUTH_CONFIG'])) {
            $connections[$i]['AUTH_CONFIG'] = json_decode(stripslashes($connections[$i]['AUTH_CONFIG']), true);
        }
    }
    $_POST['connections'] = $connections ?? [];
    FWUSystemLog::add(DPAI_KEY, [
        'type' => "save_config",
        'data' => $_POST
    ]);
    update_option(DPAI_CONFIG, $_POST);
}
$CONFIG = get_option(DPAI_CONFIG, []);
$ITEMS = $CONFIG['connections'] ?? [];
for ($i = 0; $i < count($ITEMS); $i++) {
    $ITEMS[$i]['APP_NAME'] = esc_attr($ITEMS[$i]['APP_NAME'] ?? 'Sheets API PHP');
    $ITEMS[$i]['SHEETNAME'] = esc_attr($ITEMS[$i]['SHEETNAME'] ?? '');
    $ITEMS[$i]['SPREADSHEET_ID'] = esc_attr($ITEMS[$i]['SPREADSHEET_ID'] ?? '');
}
?>
<script>
    const data = <?= json_encode($ITEMS) ?>;
    const getItem = ({
        index,
        KEY,
        APP_NAME,
        SHEETNAME,
        SPREADSHEET_ID,
        AUTH_CONFIG,
    }) => {
        let client_email = AUTH_CONFIG?.client_email

        return `
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="connections-${index}-url">
                        Url
                        <?= tooltip('Url interna de worpress para hacer peticiones') ?>
                    </label>
                </th>
                <td>
                   <span style="margin-left: auto; margin-right:1rem">
                        /wp-json/<?= DPAI_KEY ?>/send-rows?k=${KEY}
                    </span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="connections-${index}-KEY">
                        KEY
                        <?= tooltip('Clave para diferencia url de peticiones') ?>
                    </label>
                </th>
                <td>
                    <input 
                        type="text" 
                        id="connections-${index}-KEY"
                        name="connections[${index}][KEY]"
                        placeholder="KEY"
                        value="${KEY}"
                        class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="connections-${index}-APP_NAME">
                        APP_NAME
                        <?= tooltip('Nombre de la aplicación. Solo es usado internamente para identificar el cliente de Google.') ?>
                    </label>
                </th>
                <td>
                    <input 
                        type="text" 
                        id="connections-${index}-APP_NAME"
                        name="connections[${index}][APP_NAME]"
                        placeholder="Name App"
                        value="${APP_NAME}"
                        class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="connections-${index}-SHEETNAME">
                        SHEETNAME
                        <?= tooltip('Nombre de la hoja dentro del spreadsheet (ej: Hoja1, Sheet1, etc).') ?>
                    </label>
                </th>
                <td>
                    <input 
                        type="text" 
                        id="connections-${index}-SHEETNAME"
                        name="connections[${index}][SHEETNAME]"
                        placeholder="Name"
                        value="${SHEETNAME}"
                        class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="connections-${index}-SPREADSHEET_ID">
                        SPREADSHEET_ID
                        <?= tooltip('Lo encuentras en la URL del Google Sheet: https://docs.google.com/spreadsheets/d/ESTE_ID/edit') ?>
                    </label>
                </th>
                <td>
                    <input 
                        type="password"
                        id="connections-${index}-SPREADSHEET_ID"
                        name="connections[${index}][SPREADSHEET_ID]"
                        placeholder="xxxxxxxxxxx"
                        value="${SPREADSHEET_ID}"
                        class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="connections-${index}-AUTH_CONFIG">
                        AUTH_CONFIG
                        <?= tooltip('JSON del Service Account de Google Cloud. Se descarga al crear credenciales tipo "Service Account".') ?>
                    </label>
                </th>
                <td>
                    <textarea
                        id="connections-${index}-AUTH_CONFIG"
                        name="connections[${index}][AUTH_CONFIG]"
                        placeholder="{.....}"
                        class="large-text code"
                        style="min-height: 500px;"
                        rows="8">${JSON.stringify(AUTH_CONFIG, null, 2)}</textarea>
                </td>
            </tr>
            ${
                client_email ?
                `
                <tr>
                    <th>
                        PERMISOS
                    </th>
                    <td>
                        Es importante dale permisos de editor a <strong>${client_email}</strong>
                    </td>
                </tr>
                `
                :""
            }
        </table>
        `
    }
    const getCollapse = ({
        index,
        KEY='',
        APP_NAME = 'Sheets API PHP',
        SHEETNAME = '',
        SPREADSHEET_ID = '',
        AUTH_CONFIG = '',
    }) => {
        return `
            <details id="collapse-${index}">
                <summary style="display: flex;align-items:center;">
                    <span>Conexion ${index + 1} (${KEY ?? "Sin Key"})</span>
                    <span style="margin-left: auto; margin-right:1rem">
                    <button 
                        type="submit" 
                        name="delete_connection" 
                        value="${index}" 
                        class="button delete"
                        >
                        Eliminar
                    </button>
                    </span>
                </summary>
                <div>
                ${getItem({index,KEY,APP_NAME,SHEETNAME,SPREADSHEET_ID,AUTH_CONFIG})}
                </div>
            </details>
        `
    }
    const addConnection = () => {
        const contentItems = document.getElementById('contentItems')
        contentItems.innerHTML += getCollapse({
            index: contentItems.childElementCount,
            APP_NAME: "Sheets API PHP",
            SHEETNAME: "",
            SPREADSHEET_ID: "",
            AUTH_CONFIG: "",
        })
    }
    const onLoad = () => {
        const contentItems = document.getElementById('contentItems')
        for (let i = 0; i < data.length; i++) {
            contentItems.innerHTML += getCollapse({
                index: i,
                // APP_NAME,
                // SHEETNAME,
                // SPREADSHEET_ID,
                // AUTH_CONFIG,
                ...data[i]
            })

        }
    }
    document.addEventListener("DOMContentLoaded", onLoad);
</script>
<form method="post">
    <input type="hidden" name="save" value="1">
    <div id="contentItems"></div>

    <div class="content-btn">
        <button type="button" class="button button-primary" onclick="addConnection()">Agregar conexión</button>
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
