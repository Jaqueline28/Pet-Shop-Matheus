<?php
include_once("../../rotas.php"); // Inclui o arquivo de rotas
include_once($connRoute); // Inclui o arquivo de conexao

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>


  <link rel="stylesheet" href="../css-estatico/header.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css-dinamico/table.css">
  <link rel="stylesheet" href="../css-dinamico/agendamento-funcionario-cliente.css">


</head>

<body>
  

  <div class="box-informacoes">
    <img src="../img-estatico/iconCachorro.svg" alt="ícone de imagens">
    <h1>Faça o agendamento da consulta!</h1>
  </div>

  <div class="linha-campos">


    <div class="box-maior-input">

      <div class="box-input">
        <input type="text" id="cpf" name="cpf" pattern="[0-9]{11}" placeholder="Digite o CPF do cliente" required>
      </div>

      <div class="box-input">
        <select name="animais" id="animais" required>
          <option value="" disabled selected hidden>Selecione um tipo de serviço</option>
        </select>
        <button onclick="queryBanco2('animais')">Verificar</button>
      </div>


    </div>


    <div class="box-maior-input ">

      <div class="box-input">
        <select name="status" id="status" onchange="paginacao('tabelaFunAgenCli')" required style="display: none;">
          <option value="" disabled selected hidden>Selecione o status</option>
          <option value="Veterinario">Veterinário</option>
          <option value="Banho">Banho</option>
          <option value="Tosa">Tosa</option>
          <option value="Banho e Tosa">Banho e Tosa</option>
        </select>
      </div>


      <div class="box-input box-pesquisa" id="divpesq" style="display: none;">
        <input type="text" placeholder="Pesquise por um Funcionário" id="pesq">
        <button onclick="paginacao('tabelaFunAgenCli')">Pesquisar</button>
    </div>


    </div>

    <?php
    if (isset($_SESSION['agenCliFun'])) {
      echo $_SESSION['agenCliFun'];
      unset($_SESSION['agenCliFun']);
    }
    ?>

  </div>

  <div class="container box-total">

    <table id="tabela">
    </table>

    <div id="links"></div>
  
    <?php
    if (isset($_GET['pagina'])) {
        echo "<p id='pag' hidden>".$_GET['pagina']."</p>";
      } else {
        echo "<p id='pag' hidden>1</p>";
      }
    ?>

  <a href="<?php echo $agendamentoFunRoute; ?>">Voltar</a>


  <script src="<?php echo $functionsRoute ?>"></script>
</body>

</html>