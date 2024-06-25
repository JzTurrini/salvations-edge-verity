<?php
$shapes2D = [
    ['name' => 'Círculo', 'image' => './images/circle.svg'],
    ['name' => 'Quadrado', 'image' => './images/square.svg'],
    ['name' => 'Triângulo', 'image' => './images/triangle.svg']
];
$shapes3D = [
    ['name' => 'Esfera', 'component' => 'CC', 'image' => './images/sphere.svg'],
    ['name' => 'Cubo', 'component' => 'QQ', 'image' => './images/cube.svg'],
    ['name' => 'Pirâmide', 'component' => 'TT', 'image' => './images/pyramid.svg'],
    ['name' => 'Cilindro', 'component' => 'CQ', 'image' => './images/cylinder.svg'],
    ['name' => 'Cone', 'component' => 'CT', 'image' => './images/cone.svg'],
    ['name' => 'Prisma', 'component' => 'QT', 'image' => './images/prism.svg'],
];

?>

<html data-bs-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>The Traveler - Destiny 2 :: Verity</title>
    <link rel="icon" type="image/png" href="../templates/general/assets/img/favicon.png" />
    <meta name="description" content="Calculadora de símbolos e formas para o quarto encontro Veracidade (Verity) da Raid Limiar da Salvação (Edge of Salvation) da DLC A Forma Final de Destiny 2." />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="></script>
    <script src="logic.js" asp-append-version="true"></script>
    <style>
        h3 {
            padding-top: 3px;
        }

        h4 {
            background-color: #494949;
            padding: 5px 0;
        }

        .position-column {
            border: 1px solid #4e4e4e;
            border-radius: 3px;
            padding-bottom: 10px;
        }

        .symbol-img {
            width: 45px;
            height: 45px;
        }

        .small-symbol {
            width: 20px;
            height: 20px;
            filter: invert(100%);
        }

        .small-symbol.is-ok {
            filter: invert(42%) sepia(93%) saturate(1352%) hue-rotate(87deg) brightness(119%) contrast(119%);
        }
        span.is-ok {
            color: limegreen;
        }

        .symbol-img.invert {
            filter: invert(100%);
        }

        .symbol-name {
            font-size: 12px;
            margin-top: 3px;
            font-weight: bold;
        }

        .show-symbol {
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #333;
            border: 1px solid #000;
            border-radius: 3px;
            column-gap: 5px;
            cursor: pointer;
        }

        .shapes .show-symbol {
            flex: 0 0 33.3333%;
            justify-content: center;
        }

        .show-symbol.active {
            background-color: #f1f1f1;
            border: 1px solid #CCC;
            color: #333;
        }
        .show-symbol.inactive {
            background-color: #212121;
            opacity: 0.5;
            cursor: not-allowed;
        }

        .show-symbol.active .symbol-img {
            filter: invert(100%);
        }

        .show-symbol.active .symbol-img.invert {
            filter: none;
        }

        .step-by-step ul {
            width: 100%;
        }

        .stepsToSolve {
            margin-bottom: 100px;
        }

        .small-info span {
            margin-right: 5px;
            font-size: 20px;
        }
    </style>
</head>

<body>

    <div class="container">

        <header class="text-center mb-2">
            <h1>Limiar da Salvação - 4º Encontro <small>(Calculadora)</small></h1>
            <small>Desenvolvido por <a href="https://linktr.ee/jzturrini">@JzTurrini</a></small>
        </header>

        <div class="row text-center gap-2">
            <div class="col position-column">
                <h3>Esquerda</h3>

                <div class="row mb-2">
                    <h4>Dentro</h4>
                    <div class="d-flex flex-nowrap symbol-group" data-area="inside" data-position="left" role="group">
                        <?php
                        foreach ($shapes2D as $s2D) {
                            echo '<div class="show-symbol flex-fill" data-component="' . substr($s2D['name'], 0, 1) . '" data-shape="' . $s2D['name'] . '">
                                <img class="symbol-img" src="' . $s2D['image'] . '" title="' . $s2D['name'] . '" alt="' . $s2D['name'] . '" /><span class="symbol-name">' . $s2D['name'] . '</span>
                            </div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="row">
                    <h4>Fora</h4>
                    <div class="d-flex flex-wrap shapes symbol-group" data-area="outside" data-position="left" role="group">
                        <?php
                        foreach ($shapes3D as $s3D) {
                            echo '<div class="show-symbol" data-component="' . $s3D['component'] . '" data-shape="' . $s3D['name'] . '">
                                <img class="symbol-img invert" src="' . $s3D['image'] . '" title="' . $s3D['name'] . '" alt="' . $s3D['name'] . '" /><span class="symbol-name">' . $s3D['name'] . '</span>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col position-column">
                <h3>Meio</h3>
                <div class="row mb-2">
                    <h4>Dentro</h4>
                    <div class="d-flex flex-nowrap symbol-group" data-area="inside" data-position="middle" role="group">
                        <?php
                        foreach ($shapes2D as $s2D) {
                            echo '<div class="show-symbol flex-fill" data-component="' . substr($s2D['name'], 0, 1) . '" data-shape="' . $s2D['name'] . '">
                                <img class="symbol-img" src="' . $s2D['image'] . '" title="' . $s2D['name'] . '" alt="' . $s2D['name'] . '" /><span class="symbol-name">' . $s2D['name'] . '</span>
                            </div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="row">
                    <h4>Fora</h4>
                    <div class="d-flex flex-wrap shapes symbol-group" data-area="outside" data-position="middle" role="group">
                        <?php
                        foreach ($shapes3D as $s3D) {
                            echo '<div class="show-symbol" data-component="' . $s3D['component'] . '" data-shape="' . $s3D['name'] . '">
                                <img class="symbol-img invert" src="' . $s3D['image'] . '" title="' . $s3D['name'] . '" alt="' . $s3D['name'] . '" /><span class="symbol-name">' . $s3D['name'] . '</span>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col position-column">
                <h3>Direita</h3>
                <div class="row mb-2">
                    <h4>Dentro</h4>
                    <div class="d-flex flex-nowrap symbol-group" data-area="inside" data-position="right" role="group">
                        <?php
                        foreach ($shapes2D as $s2D) {
                            echo '<div class="show-symbol flex-fill" data-component="' . substr($s2D['name'], 0, 1) . '" data-shape="' . $s2D['name'] . '">
                                <img class="symbol-img" src="' . $s2D['image'] . '" title="' . $s2D['name'] . '" alt="' . $s2D['name'] . '" /><span class="symbol-name">' . $s2D['name'] . '</span>
                            </div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="row">
                    <h4>Fora</h4>
                    <div class="d-flex flex-wrap shapes symbol-group" data-area="outside" data-position="right" role="group">
                        <?php
                        foreach ($shapes3D as $s3D) {
                            echo '<div class="show-symbol" data-component="' . $s3D['component'] . '" data-shape="' . $s3D['name'] . '">
                                <img class="symbol-img invert" src="' . $s3D['image'] . '" title="' . $s3D['name'] . '" alt="' . $s3D['name'] . '" /><span class="symbol-name">' . $s3D['name'] . '</span>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-grid mt-2">
            <button class="btn btn-primary btn-reset" type="button">Resetar</button>
        </div>
        <hr />
        <div class="row stepsToSolve">
            <h4>Passo à Passo para Resolver</h4>
            <div class="d-flex step-by-step">
                Texto...
            </div>
        </div>
    </div>


    <script async src="https://www.googletagmanager.com/gtag/js?id=G-5569XCCN7L"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag("js", new Date());
        gtag("config", "G-5569XCCN7L");
    </script>
</body>

</html>