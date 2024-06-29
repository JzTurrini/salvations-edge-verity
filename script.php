<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, HEAD, OPTIONS');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers');
// -----------------------------------------------------------------------------
header('Content-type:application/json; charset=utf8');

class finalShape
{
    // Variáveis globais
    private $shape2D = [
        ['name' => 'Círculo'],
        ['name' => 'Quadrado'],
        ['name' => 'Triângulo']
    ];
    private $shape3D = [];
    private $positions = [
        'left' => ['inside' => null, 'outside' => null],
        'middle' => ['inside' => null, 'outside' => null],
        'right' => ['inside' => null, 'outside' => null],
    ];
    private $positionsToShape = [
        'left' => null,
        'middle' => null,
        'right' => null
    ];
    private $insideShapeAbbr = "";
    private $finalShapes = [];
    private $logs = [];
    private $dissection = [];
    private $positionsList = ['left', 'middle', 'right'];
    private $positionTranslate = ['left' => 'Esquerda', 'middle' => 'Meio', 'right' => 'Direita'];

    public function __construct($leftInside, $middleInside, $rightInside, $leftOut, $middleOut, $rightOut)
    {
        // Definindo as formas 3D.
        $this->shape3D = [
            ['name' => 'Esfera', 'isPure' => true, 'symbol1' => 'Círculo', 'symbol2' => 'Círculo', 'image' => './images/sphere.svg'],
            ['name' => 'Cubo', 'isPure' => true, 'symbol1' => 'Quadrado', 'symbol2' => 'Quadrado', 'image' => './images/cube.svg'],
            ['name' => 'Pirâmide', 'isPure' => true, 'symbol1' => 'Triângulo', 'symbol2' => 'Triângulo', 'image' => './images/pyramid.svg'],
            ['name' => 'Cilindro', 'isPure' => false, 'symbol1' => 'Círculo', 'symbol2' => 'Quadrado', 'image' => './images/cylinder.svg'],
            ['name' => 'Cone', 'isPure' => false, 'symbol1' => 'Círculo', 'symbol2' => 'Triângulo', 'image' => './images/cone.svg'],
            ['name' => 'Prisma', 'isPure' => false, 'symbol1' => 'Quadrado', 'symbol2' => 'Triângulo', 'image' => './images/prism.svg'],
        ];

        $this->finalShapes = [
            ['abbrv' => 'CTQ', 'combination' => [$this->arraySearchByColumn($this->shape3D, 'Prisma'), $this->arraySearchByColumn($this->shape3D, 'Cilindro'), $this->arraySearchByColumn($this->shape3D, 'Cone')]],
            ['abbrv' => 'CQT', 'combination' => [$this->arraySearchByColumn($this->shape3D, 'Prisma'), $this->arraySearchByColumn($this->shape3D, 'Cone'), $this->arraySearchByColumn($this->shape3D, 'Cilindro')]],
            ['abbrv' => 'TQC', 'combination' => [$this->arraySearchByColumn($this->shape3D, 'Cilindro'), $this->arraySearchByColumn($this->shape3D, 'Cone'), $this->arraySearchByColumn($this->shape3D, 'Prisma')]],
            ['abbrv' => 'TCQ', 'combination' => [$this->arraySearchByColumn($this->shape3D, 'Cilindro'), $this->arraySearchByColumn($this->shape3D, 'Prisma'), $this->arraySearchByColumn($this->shape3D, 'Cone')]],
            ['abbrv' => 'QCT', 'combination' => [$this->arraySearchByColumn($this->shape3D, 'Cone'), $this->arraySearchByColumn($this->shape3D, 'Prisma'), $this->arraySearchByColumn($this->shape3D, 'Cilindro')]],
            ['abbrv' => 'QTC', 'combination' => [$this->arraySearchByColumn($this->shape3D, 'Cone'), $this->arraySearchByColumn($this->shape3D, 'Cilindro'), $this->arraySearchByColumn($this->shape3D, 'Prisma')]],
        ];

        // Definindo com base na escolha do usuário.
        $this->positions['left']['inside'] = $this->arraySearchByColumn($this->shape2D, $leftInside);
        $this->positions['middle']['inside'] = $this->arraySearchByColumn($this->shape2D, $middleInside);
        $this->positions['right']['inside'] = $this->arraySearchByColumn($this->shape2D, $rightInside);
        $this->positions['left']['outside'] = $this->arraySearchByColumn($this->shape3D, $leftOut);
        $this->positions['middle']['outside'] = $this->arraySearchByColumn($this->shape3D, $middleOut);
        $this->positions['right']['outside'] = $this->arraySearchByColumn($this->shape3D, $rightOut);

        // Definindo as posições iniciais que serão alteradas depois.
        $this->positionsToShape = [
            'left' => $this->arraySearchByColumn($this->shape3D, $leftOut),
            'middle' => $this->arraySearchByColumn($this->shape3D, $middleOut),
            'right' => $this->arraySearchByColumn($this->shape3D, $rightOut)
        ];

        // Agora eu rodo a validação para ver se o usuário informou tudo o que tinha que ser informado.
        $isValid = $this->isCombinationValid($leftOut, $middleOut, $rightOut);
        if (!$isValid) {
            echo json_encode(["Aguardando Formas 3D"]);
        } else {
            // Usando isso para descobrir a forma final baseado na combinação inicial.
            $this->insideShapeAbbr = substr($leftInside, 0, 1) . substr($middleInside, 0, 1) . substr($rightInside, 0, 1);
        }
    }

    public function isCombinationValid($leftName, $middleName, $rightName)
    {
        // Encontrar os objetos correspondentes às formas pelos nomes
        $left = $this->arraySearchByColumn($this->shape3D, $leftName);
        $middle = $this->arraySearchByColumn($this->shape3D, $middleName);
        $right = $this->arraySearchByColumn($this->shape3D, $rightName);

        if ($left === false || $middle === false || $right === false) {
            echo json_encode(["Forma 3D não encontrada"]);
        }

        $symbolCounts = [
            'Quadrado' => 0,
            'Círculo' => 0,
            'Triângulo' => 0,
        ];

        // Função auxiliar para atualizar contagens
        $updateCounts = function ($shape) use (&$symbolCounts) {
            $symbolCounts[$shape['symbol1']]++;
            $symbolCounts[$shape['symbol2']]++;
        };

        // Atualiza contagens para cada posição
        $updateCounts($left);
        $updateCounts($middle);
        $updateCounts($right);

        // Verifica se tem exatamente 2 de cada símbolo
        return (
            $symbolCounts['Quadrado'] === 2 &&
            $symbolCounts['Círculo'] === 2 &&
            $symbolCounts['Triângulo'] === 2
        );
    }

    private function arraySearchByColumn($where, $shapeName)
    {
        $index = array_search($shapeName, array_column($where, 'name'));
        return $index !== false ? $where[$index] : false;
    }

    private function defineDissection()
    {
        // Sempre inicia a dissecação do zero.
        $this->dissection = [];
        foreach ($this->positionsList as $element) {
            $shape2D = $this->positions[$element]['inside']['name'];
            $shape3D = $this->positionsToShape[$element]['name'];
            $actual = $this->arraySearchByColumn($this->shape3D, $shape3D);
            $shapesOfActual = [$actual['symbol1'], $actual['symbol2']];

            if (in_array($shape2D, $shapesOfActual) || $actual['isPure']) {
                $totalMoves = $actual['isPure'] ? 2 : $this->countOccurrences($shapesOfActual, $shape2D);

                for ($i = 1; $i <= $totalMoves; $i++) {
                    $this->dissection[] = [
                        'where' => $element,
                        'what' => $actual['isPure'] ? $actual['symbol1'] : $shape2D,
                        'shapes' => $shapesOfActual,
                    ];
                }
            }
        }
        //print_r($this->dissection);

        if (!$this->checkFinalShapes() && $this->dissection) {
            $this->dissect();
        } else {
            echo json_encode($this->logs);
        }
    }

    private function dissect()
    {
        foreach ($this->dissection as $dissect) {
            $allShapes = array_merge([$dissect['what']], $dissect['shapes']);
            $missingShapes = array_filter($this->shape2D, function ($element) use ($allShapes) {
                return !in_array($element['name'], $allShapes);
            });

            if ($missingShapes) {
                foreach ($missingShapes as $theShape) {
                    $logTotal = count($this->logs);
                    $whereToDissect = array_filter($this->dissection, function ($element) use ($dissect, $theShape) {
                        return $element['where'] !== $dissect['where'] && $theShape['name'] == $element['what'];
                    });
                    // Só vou pegar de outra forma para fechar a atual se eu já não tiver encontrado uma perfeita.
                    if (!$whereToDissect) {
                        $whereToDissect = array_filter($this->dissection, function ($element) use ($dissect, $theShape) {
                            return $element['where'] !== $dissect['where'] && in_array($theShape['name'], $element['shapes']);
                        });
                    }

                    if (!empty($whereToDissect)) {
                        $whereToDissect = array_shift($whereToDissect);
                        $indexOfOrigin = array_search($dissect['what'], $dissect['shapes']);
                        $indexOfDestination = array_search($whereToDissect['what'], $whereToDissect['shapes']);


                        $newOriginShapes = $dissect['shapes'];
                        $newDestinationShapes = $whereToDissect['shapes'];
                        $newOriginShapes[$indexOfOrigin] = $whereToDissect['shapes'][$indexOfDestination];
                        $newDestinationShapes[$indexOfDestination] = $dissect['shapes'][$indexOfOrigin];

                        // Após trocar eu atualizo as formas.
                        $this->positionsToShape[$dissect['where']] = $this->arraySearchByColumn($this->shape3D, $this->findShapeBySymbols($newOriginShapes));
                        $this->positionsToShape[$whereToDissect['where']] = $this->arraySearchByColumn($this->shape3D, $this->findShapeBySymbols($newDestinationShapes));
                        $shapesToLog = $this->displayPositionsToShape();

                        $this->logs[] = ($logTotal + 1) . 'º - Dissecar o <u>' . $dissect['what'] . "</u> da posição <strong>" . $this->positionTranslate[$dissect['where']] . "</strong> e depois.";
                        $this->logs[] = ($logTotal + 2) . 'º - Dissecar o <u>' . $whereToDissect['what'] . "</u> da posição <strong>" . $this->positionTranslate[$whereToDissect['where']] . "</strong>." . $shapesToLog . '<hr />';

                        $this->defineDissection();
                        return;
                    }
                }
            }
        }
    }

    private function displayPositionsToShape()
    {
        $finalShapes = array_filter($this->finalShapes, function ($shape) {
            return $shape['abbrv'] === $this->insideShapeAbbr;
        });
        $finalShapes = array_shift($finalShapes);
        $gameShapes = [];
        foreach ($finalShapes['combination'] as $element) {
            $gameShapes[] = $element;
        }

        $return = '<div class="small-info">';
        foreach ($this->positionsList as $element) {
            $positionT = $this->positionTranslate[$element];
            $shape = $this->positionsToShape[$element];
            $isOk = $shape['name'] == $gameShapes[array_search($element, $this->positionsList)]['name'];
            $return .= ' <span class="' . ($isOk ? 'is-ok' : '') . '"><strong>' . $positionT . '</strong>: <img class="small-symbol ' . ($isOk ? 'is-ok' : '') . '" src="' . $shape['image'] . '" alt="' . $shape['name'] . '" title="' . $shape['name'] . '"/></span> ';
        }
        $return .= "</div>";
        return $return;
    }

    private function countOccurrences($array, $targetString)
    {
        return array_reduce($array, function ($count, $current) use ($targetString) {
            return $count + (($current === $targetString) ? 1 : 0);
        }, 0);
    }

    private function areArraysIdentical($array1, $array2)
    {
        return $array1 === $array2;
    }

    private function checkFinalShapes()
    {
        $finalShapes = array_filter($this->finalShapes, function ($shape) {
            return $shape['abbrv'] === $this->insideShapeAbbr;
        });
        $finalShapes = array_shift($finalShapes);
        $userShapes = [];
        $gameShapes = [];

        foreach ($this->positionsList as $element) {
            $userShapes[] = $this->positionsToShape[$element];
        }

        foreach ($finalShapes['combination'] as $element) {
            $gameShapes[] = $element;
        }

        return $this->areArraysIdentical($gameShapes, $userShapes);
    }

    public function findShapeBySymbols($symbols)
    {
        foreach ($this->shape3D as $shape) {
            $symbol1 = $shape['symbol1'];
            $symbol2 = $shape['symbol2'];

            // Verifica se os símbolos estão presentes na ordem dada ou invertida
            if (($symbol1 === $symbols[0] && $symbol2 === $symbols[1]) ||
                ($symbol1 === $symbols[1] && $symbol2 === $symbols[0])
            ) {
                return $shape['name'];
            }
        }

        // Caso não encontre, retorna null ou lança uma exceção, dependendo do seu fluxo de aplicação
        return null;
    }

    public function solve()
    {
        $this->logs = [];
        $this->defineDissection();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leftInside = trim($_POST['leftInside']);
    $middleInside = trim($_POST['middleInside']);
    $rightInside = trim($_POST['rightInside']);
    $leftOut = trim($_POST['leftOut']);
    $middleOut = trim($_POST['middleOut']);
    $rightOut = trim($_POST['rightOut']);

    // Executando a função.
    try {
        $finalShape = new finalShape(
            $leftInside,
            $middleInside,
            $rightInside,
            $leftOut,
            $middleOut,
            $rightOut
        );
        $finalShape->solve();
    } catch (Exception $e) {
        echo json_encode(["Ocorreu um erro ao tentar listar os passos: ". $e->getMessage() . "."]);
    }
}
