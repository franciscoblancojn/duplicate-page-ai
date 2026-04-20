<?php

function tooltip($text)
{
    return '
    <span class="goshap-tooltip">
        <span class="dashicons dashicons-info"></span>
        <span class="goshap-tooltip-text">' . $text . '</span>
    </span>';
}
function parseError($text)
{
    return preg_replace(
        '/(https?:\/\/[^\s]+)/',
        '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
        $text
    );
}
$TAGS = [
    [
        'key' => 'config',
        'title' => 'Configuracion IA',
    ],
    [
        'key' => 'duplication',
        'title' => 'Duplicacion de Paginas',
    ],
];

$DPAI_CONFIG = new DPAI_CONFIG();

$defaultTag =  $TAGS[0]['key'];

$CONFIG = $DPAI_CONFIG->getConfig();
?>
<div class="wrap">
    <h1>Duplicate Page AI</h1>
    <div class="nav-tab-wrapper woo-nav-tab-wrapper">
        <?php
        foreach ($TAGS  as $key => $value) {
        ?>
            <a
                class="nav-tab <?= $value['key'] == $defaultTag ? "nav-tab-active" : "" ?>"
                data-tab="<?= $value['key'] ?>"
                href="#tag-<?= $value['key'] ?>">
                <?= $value['title'] ?>
            </a>
        <?php
        }
        ?>
    </div>
    <?php
    foreach ($TAGS  as $key => $value) {
    ?>
        <div class="tab-content <?= $value['key'] == $defaultTag ? "nav-tab-active" : "" ?>" id="<?= $value['key'] ?>">
            <?php
            require_once DPAI_DIR . 'src/page/sections/' . $value['key'] . ".php";
            ?>
        </div>
    <?php
    }
    ?>
    <style>
        .tab-content:not(.nav-tab-active) {
            display: none;
        }
        .tab-content {
            padding-top: 1rem;
        }
        .nav-tab {
            cursor: pointer;
        }
        .error{
            color: #ffffffff;
            background: #d63638;
            font-weight: 900;
            position: sticky;
            left: 0;
            bottom: .5rem;
            padding: 1rem;
            border-radius: .5rem;
        }
    </style>
    <script>
        document.querySelectorAll('.nav-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.nav-tab, .tab-content')
                    .forEach(el => el.classList.remove('nav-tab-active'));

                btn.classList.add('nav-tab-active');
                document.getElementById(btn.dataset.tab)
                    .classList.add('nav-tab-active');
            });
        });
        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash
            if (hash) {
                const btn = document.querySelector(".nav-tab[href='" + hash + "']")
                if (btn) {
                    btn?.click()
                }
            }
        });
    </script>
</div>
<?php
