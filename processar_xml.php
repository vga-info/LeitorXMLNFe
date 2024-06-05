<html>
<head>
    <title>Processar XML</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Resto do seu conteúdo -->
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["arquivo"])) {
    $xml = simplexml_load_file($_FILES["arquivo"]["tmp_name"]);

    // Extrair informações do cabeçalho
    $cnpjEmitente = $xml->NFe->infNFe->emit->CNPJ;
    $xNomeEmitente = $xml->NFe->infNFe->emit->xNome;
    $cNF = $xml->NFe->infNFe->ide->cNF;
    $serie = $xml->NFe->infNFe->ide->serie;
    $dhEmi = $xml->NFe->infNFe->ide->dhEmi;
    $natOp = $xml->NFe->infNFe->ide->natOp;
    $chaveAcesso = (string)$xml->NFe->infNFe['Id'];

    // Formatar a data e hora de emissão para "DD/MM/AAAA HH:MM"
    $dhEmiFormatted = date("d/m/Y H:i", strtotime($dhEmi));

    // Exibir informações do remetente como um cabeçalho
    echo "<h2 class='header'>Informações do Remetente:</h2>";
    echo "CNPJ: $cnpjEmitente | $xNomeEmitente | Operação: $natOp";
    echo "<br>";
    echo "Número da NF: $cNF | Série: $serie | Data de Emissão: $dhEmiFormatted <br> Chave: $chaveAcesso";

    // Botão de impressão
    echo ' <button onclick="window.print()" class="print-button"><i class="fas fa-print"></i></button>';
    echo ' <button onclick="window.location.href=\'xml.html\'" class="back-button"><i class="fas fa-arrow-left"></i></button>';


    // Iniciar a tabela de produtos
    echo "<h2 class='header'>Produtos:</h2>";
    echo "<style>
            .ncm-highlight {
                background-color: gray;
                color: white;
            }
            .header {
                background-color: #ffffff;
                color: black;
                padding: 10px;
                text-align: center;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #ccc;
                padding: 8px;
            }
            .back-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff; /* Cor de fundo do botão */
            color: #fff; /* Cor do texto do botão */
            border: none;
            border-radius: 5px;
            cursor: pointer;
             }

            .back-button:hover {
             background-color: #0056b3; /* Cor de fundo do botão ao passar o mouse */
            }

            .print-button {
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    background-color: #27ae60; /* Cor de fundo do botão de impressão */
                    color: #fff; /* Cor do texto do botão de impressão */
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                }

                .print-button:hover {
                    background-color: #1d9957; /* Cor de fundo do botão de impressão ao passar o mouse */
                }



          </style>";
    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>Código</th>";
    echo "<th>Nome</th>";
    echo "<th>QTD</th>";
    echo "<th>NCM</th>";
    echo "<th>CFOP</th>";
    if (isset($xml->NFe->infNFe->det[0]->prod->rastro->nLote)) {
        echo "<th>Nº Lote</th>";
    }
    if (isset($xml->NFe->infNFe->det[0]->prod->rastro->qLote)) {
        echo "<th>QTD Lote</th>";
    }
    if (isset($xml->NFe->infNFe->det[0]->prod->rastro->dFab)) {
        echo "<th>Fabricação</th>";
    }
    if (isset($xml->NFe->infNFe->det[0]->prod->rastro->dVal)) {
        echo "<th>Validade</th>";
    }
    if (isset($xml->NFe->infNFe->det[0]->prod->med->cProdANVISA)) {
        echo "<th>ANVISA</th>";
    }
    echo "</tr>";

    // Processar os produtos
    foreach ($xml->NFe->infNFe->det as $produto) {
        $cProd = $produto->prod->cProd;
        $xProd = $produto->prod->xProd;
        $qCom = intval($produto->prod->qCom); // Converter a quantidade para inteiro
        $NCM = $produto->prod->NCM;
        $CFOP = $produto->prod->CFOP;

        // Verificar se o NCM começa com "300" e definir uma classe CSS para destaque
        $ncmClass = (strpos($NCM, "300") === 0) ? "ncm-highlight" : "";

        // Iniciar a linha da tabela
        echo "<tr>";
        echo "<td>$cProd</td>";
        echo "<td>$xProd</td>";
        echo "<td style='text-align: center;'>$qCom</td>"; // Centralizar a quantidade
        echo "<td class='$ncmClass'>$NCM</td>";
        echo "<td>$CFOP</td>";

        // Verificar e exibir Nº Lote
        if (isset($produto->prod->rastro->nLote)) {
            echo "<td>{$produto->prod->rastro->nLote}</td>";
        } else {
            echo "<td></td>";
        }

        // Verificar e exibir QTD Lote
        if (isset($produto->prod->rastro->qLote)) {
            echo "<td>{$produto->prod->rastro->qLote}</td>";
        } else {
            echo "<td></td>";
        }

        // Verificar e exibir Fabricação
        if (isset($produto->prod->rastro->dFab)) {
            echo "<td>" . date("d/m/Y", strtotime($produto->prod->rastro->dFab)) . "</td>";
        } else {
            echo "<td></td>";
        }

        // Verificar e exibir Validade
        if (isset($produto->prod->rastro->dVal)) {
            echo "<td>" . date("d/m/Y", strtotime($produto->prod->rastro->dVal)) . "</td>";
        } else {
            echo "<td></td>";
        }

        // Verificar e exibir ANVISA
        if (isset($produto->prod->med->cProdANVISA)) {
            echo "<td>{$produto->prod->med->cProdANVISA}</td>";
        } else {
            echo "<td></td>";
        }

        // Fechar a linha da tabela
        echo "</tr>";
    }

    // Fechar a tabela
    echo "</table>";

    echo "<center><br>
            Sistema Leitor XML 1.1 (11/10/2023)
            <br>
            Desenvolvido por Equipe de T.I. DentalMV
            </center>";
} else {
    echo "Por favor, selecione um arquivo XML para processar.";
}
?>
