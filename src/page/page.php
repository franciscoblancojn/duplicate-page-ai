<?php
$TAGS = [
    [
        'key' => 'config',
        'title' => 'Configuracion AI',
    ],
    [
        'key' => 'duplication',
        'title' => 'Duplicacion de Paginas',
    ],
];

$defaultTag =  $TAGS[0]['key'];
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
