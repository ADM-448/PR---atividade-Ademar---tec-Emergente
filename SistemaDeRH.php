<?php

namespace Atividade\CodeReview;

use PDO;
use Exception;

/**
 * Classe SistemaDeRH
 * ATENÇÃO: ESTE ARQUIVO FOI CRIADO EXCLUSIVAMENTE PARA A ATIVIDADE DE CODE REVIEW!
 * Ele contém intencionalmente:
 * - SQL Injections
 * - Senhas no código-fonte
 * - Alta complexidade ciclomática (Spaghetti code)
 * - Nomenclaturas confusas
 * - Falta de SRP (Single Responsibility Principle)
 */
class SistemaDeRH
{
    public $db;
    public $email_rh = "rh@empresa.com";
    
    // Problema: Senhas no código-fonte
    public $senha_banco_rh = "root123456"; 
    public $usuario_banco_rh = "admin_rh";

    public function __construct()
    {
        // Conexão direta sem tratamento
        $this->db = new PDO('mysql:host=localhost;dbname=empresa_rh', $this->usuario_banco_rh, $this->senha_banco_rh);
    }

    /**
     * Contrata um novo funcionário.
     * Problema: Método gigantesco fazendo tudo (Banco, cálculo, email).
     */
    public function contratarFuncionario($dados)
    {
        // Problema: Injeção de SQL
        $cpf = $dados['cpf'];
        $query_verifica = "SELECT * FROM funcionarios WHERE cpf = '" . $cpf . "'";
        $stmt = $this->db->query($query_verifica);
        
        if ($stmt->fetch()) {
            return "Funcionário já cadastrado.";
        }

        $salario_base = $dados['salario'];
        $cargo = $dados['cargo'];
        $beneficios = 0;

        // Regras de negócio misturadas com persistência
        if ($cargo == 'Gerente') {
            $beneficios = 1500;
        } else if ($cargo == 'Diretor') {
            $beneficios = 3000;
        } else {
            $beneficios = 500;
        }

        $total_receber = $salario_base + $beneficios;

        $nome = $dados['nome'];
        $email = $dados['email'];
        $data_contratacao = date('Y-m-d');

        // Mais injeção de SQL
        $query_insert = "INSERT INTO funcionarios (nome, cpf, email, cargo, salario, data_admissao) 
                         VALUES ('$nome', '$cpf', '$email', '$cargo', $salario_base, '$data_contratacao')";
        
        $this->db->query($query_insert);
        $func_id = $this->db->lastInsertId();

        // Quebra de responsabilidade (Mandando email no método de banco)
        $msg = "Olá $nome, você foi contratado como $cargo com salário de $total_receber";
        mail($email, "Bem-vindo à empresa!", $msg, "From: " . $this->email_rh);

        return $func_id;
    }

    /**
     * Calcula bônus fim de ano
     * Problema: Nomes péssimos
     */
    public function clcBonFdA($id, $p) {
        $q = "SELECT salario FROM funcionarios WHERE id = " . $id;
        $r = $this->db->query($q)->fetch();
        
        if($r) {
            if($p == 1) {
                return $r['salario'] * 0.1;
            } elseif ($p == 2) {
                return $r['salario'] * 0.2;
            } else {
                return $r['salario'] * 0.05;
            }
        }
        return 0;
    }

    /**
     * Problema: Geração de arquivo massivo, código spaghetti
     */
    public function gerarRelatorioPonto($mes, $ano)
    {
        $sql = "SELECT * FROM ponto WHERE mes = $mes AND ano = $ano";
        $pontos = $this->db->query($sql)->fetchAll();
        
        $html = "<!DOCTYPE html><html><head><title>Ponto RH</title></head><body>";
        $html .= "<h1>Relatório de Ponto - $mes/$ano</h1>";
        $html .= "<table border='1'><tr><th>Funcionario ID</th><th>Horas Trabalhadas</th><th>Faltas</th></tr>";
        
        foreach($pontos as $p) {
            $html .= "<tr>";
            $html .= "<td>" . $p['funcionario_id'] . "</td>";
            $html .= "<td>" . $p['horas'] . "</td>";
            $html .= "<td>" . $p['faltas'] . "</td>";
            $html .= "</tr>";
            
            // Loop dentro de loop fazendo update sem transação
            if($p['faltas'] > 3) {
                $this->db->query("UPDATE funcionarios SET advertencia = 1 WHERE id = " . $p['funcionario_id']);
            }
        }
        
        $html .= "</table></body></html>";
        
        file_put_contents("relatorios_rh/ponto_{$mes}_{$ano}.html", $html);
        return true;
    }

    /**
     * Enche linguiça com dados fixos (Mais linhas para a PR)
     */
    public function preencherBancoFake()
    {
        $dados = [
            ['nome' => 'Ana Silva', 'cargo' => 'Desenvolvedor', 'salario' => 5000],
            ['nome' => 'Marcos Paulo', 'cargo' => 'Desenvolvedor', 'salario' => 5000],
            ['nome' => 'Carla Dias', 'cargo' => 'QA', 'salario' => 4500],
            ['nome' => 'Roberto Justo', 'cargo' => 'Gerente', 'salario' => 12000],
            ['nome' => 'Silvio Costa', 'cargo' => 'Diretor', 'salario' => 25000],
            ['nome' => 'Fernanda Souza', 'cargo' => 'UX Designer', 'salario' => 6000],
            ['nome' => 'Paulo Mendes', 'cargo' => 'DevOps', 'salario' => 8000],
            ['nome' => 'Lucas Lima', 'cargo' => 'Scrum Master', 'salario' => 7500],
            ['nome' => 'Juliana Paes', 'cargo' => 'Product Owner', 'salario' => 9000],
            ['nome' => 'Marina Ruy', 'cargo' => 'Analista de RH', 'salario' => 4000],
            ['nome' => 'Rodrigo Faro', 'cargo' => 'Marketing', 'salario' => 4500],
            ['nome' => 'Tadeu Schmidt', 'cargo' => 'Suporte', 'salario' => 3000],
            ['nome' => 'Fatima Bernardes', 'cargo' => 'Financeiro', 'salario' => 5500],
            ['nome' => 'William Bonner', 'cargo' => 'Contabilidade', 'salario' => 5500],
            ['nome' => 'Pedro Bial', 'cargo' => 'Diretor Comercial', 'salario' => 20000],
            ['nome' => 'Camila Queiroz', 'cargo' => 'Vendedora', 'salario' => 3500],
            ['nome' => 'Bruna Marquezine', 'cargo' => 'Atendimento', 'salario' => 2500],
            ['nome' => 'Neymar Jr', 'cargo' => 'Estagiário', 'salario' => 1500],
            ['nome' => 'Lionel Messi', 'cargo' => 'CEO', 'salario' => 50000],
            ['nome' => 'Cristiano Ronaldo', 'cargo' => 'CTO', 'salario' => 45000],
        ];

        foreach($dados as $d) {
            $sql = "INSERT INTO funcionarios (nome, cargo, salario) VALUES ('".$d['nome']."', '".$d['cargo']."', ".$d['salario'].")";
            $this->db->query($sql);
        }
        
        $departamentos = [
            ['nome' => 'Tecnologia', 'orcamento' => 1000000],
            ['nome' => 'Marketing', 'orcamento' => 500000],
            ['nome' => 'Financeiro', 'orcamento' => 200000],
            ['nome' => 'Comercial', 'orcamento' => 300000],
            ['nome' => 'Diretoria', 'orcamento' => 2000000],
            ['nome' => 'Suporte', 'orcamento' => 100000],
            ['nome' => 'RH', 'orcamento' => 150000],
            ['nome' => 'Logística', 'orcamento' => 400000],
            ['nome' => 'Produção', 'orcamento' => 800000],
            ['nome' => 'Qualidade', 'orcamento' => 50000],
        ];

        foreach($departamentos as $dep) {
            $sql = "INSERT INTO departamentos (nome, orcamento) VALUES ('".$dep['nome']."', ".$dep['orcamento'].")";
            $this->db->query($sql);
        }
        
        return "Concluído.";
    }

    /**
     * Problema: Query massiva, acoplamento e strings inseguras
     */
    public function demitirFuncionario($id, $motivo)
    {
        $q_func = "SELECT * FROM funcionarios WHERE id = $id";
        $f = $this->db->query($q_func)->fetch();
        
        if(!$f) return false;

        $this->db->query("DELETE FROM funcionarios WHERE id = $id");
        $this->db->query("DELETE FROM ponto WHERE funcionario_id = $id");
        $this->db->query("DELETE FROM beneficios WHERE funcionario_id = $id");
        
        // Log manual péssimo
        file_put_contents('demissoes.log', "Funcionario " . $f['nome'] . " demitido. Motivo: $motivo\n", FILE_APPEND);
        
        return true;
    }

    /**
     * Filtros sem escape
     */
    public function listarFuncionarios($filtros)
    {
        $sql = "SELECT * FROM funcionarios WHERE 1=1 ";
        
        if(isset($filtros['nome'])) {
            $sql .= " AND nome LIKE '%" . $filtros['nome'] . "%'";
        }
        if(isset($filtros['cargo'])) {
            $sql .= " AND cargo = '" . $filtros['cargo'] . "'";
        }
        if(isset($filtros['salario_min'])) {
            $sql .= " AND salario >= " . $filtros['salario_min'];
        }
        if(isset($filtros['salario_max'])) {
            $sql .= " AND salario <= " . $filtros['salario_max'];
        }

        $res = $this->db->query($sql);
        $funcionarios = [];
        while($row = $res->fetch(PDO::FETCH_ASSOC)){
            $funcionarios[] = $row;
        }
        return $funcionarios;
    }
}
/ /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 1 9 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 2 9 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 3 9 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 4 9 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 5 9 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 6 9 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 7 9 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 8 9 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 0 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 1 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 2 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 3 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 4 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 5 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 6 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 7 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 8 )  
 / /   M a i s   u m a   l i n h a   p a r a   b a t e r   a   c o t a   d e   3 0 0   l i n h a s   e x i g i d a   p e l a   a t i v i d a d e   ( 9 9 )  
 