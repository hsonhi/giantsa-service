<?php
include('connection/connection.class.php');
/**
* Client class verify what method was sent and execute the respective method.
*/
class Client
{
	//Attributes
	private $id;
	private $name;
	private $age;
	private $gender;
	private $db;
	private $method;
	function __construct($name = '', $age = '', $gender = '')
	{
		# Construct the class and set the values in the attributes.
		$this->db = ConnectionDB::getInstance();
	}

	function verifyMethod($method,$route,$data){
		switch ($method) {
		case 'GET':
			# GetAll
			if (count($route) <= 3) {
				$routValid = $route[1];
				if($routValid == "vehicles" || $routValid == "cstatus" || $routValid == "city" || $routValid == "job" || $routValid == "clients" || $routValid == "apolicetipos" || $routValid == "apolicevalores" || $routValid == "apolices" || $routValid == "sinistros" || $routValid == "arquivos")
			{
				return self::doGet($route);
			}else{
				return array('status' => 'Parâmetros de busca inválido!');
			}
			}else{
				$arr_json = array('status' => 'Routing de parâmetros inválido!');
			}
			break;
		case 'POST':
			# InsertClients
			if(isset($data["clients"])){
				if(empty(self::validatedoInsertClients($data))){ 
					return self::doInsertClients($data); 
				}else{
					return array('status' => self::validatedoInsertClients($data)); 
				}
           # InsertApolices
			}else if(isset($data["apolices"])){
				if(empty(self::validatedoInsertApolicies($data))){ 
					return self::doInsertApolicies($data); 
				}else{
					return array('status' => self::validatedoInsertApolicies($data)); 
				}
			# InsertSinistros
			}else if(isset($data["sinistros"])){
				if(empty(self::validatedoInsertSinistros($data))){ 
					return self::doInsertSinistros($data); 
				}else{
					return array('status' => self::validatedoInsertSinistros($data)); 
				}
			# InsertSinistrosArquivos
			}else if (isset($_FILES['photo'])) {
				if(empty(self::validatedoInsertSinistroArquivos())){ 
					return self::doInsertSinistroArquivos(); 
				}else{
					return array('status' => self::validatedoInsertSinistroArquivos()); 
				}	
			}else{
				return array('status' => 'POST request inválido!'); 
			}
			break;
		case 'DELETE':
			# DeleteAll
			if(!self::validateDelete($data)){
				return self::doDelete($data); 
			}else{
				return array('status' => 'Formato de dados json para remoção inválida!'); 
			}
			break;		
		default:
			return array('status' => 405);
      		break;
		}
	}

	function doGet($route){

		switch ($route[1]) {
			case ("vehicles"):
				$sql = 'CALL GETVEHICLES(:id)';
				break;
		    case ("cstatus"):
				$sql = 'CALL GETCSTATUS(:id)';
				break;
			case ("city"):
				$sql = 'CALL GETCITY(:id)';
				break;
			case ("job"):
				$sql = 'CALL GETJOB(:id)';
				break;
			case ("clients"):
				$sql = 'CALL GETCLIENTS(:id)';
				break;
			case ("apolices"):
				$sql = 'CALL GETAPOLICES(:id)';
				break;
			case ("apolicetipos"):
				$sql = 'CALL GETAPOLICETIPO(:id)';
				break;
			case ("apolicevalores"):
				$sql = 'CALL GETAPOLICETIPOVALORES(:id)';
				break;
			case ("sinistros"):
				$sql = 'CALL GETSINISTROS(:id)';
					break;
			case ("arquivos"):
				$sql = 'CALL GETSINISTROSARQUIVOS(:id)';
					break;
			default:
			$sql = 'CALL GETSINISTROS(:id)';
				break;
		}
	    $stmt = $this->db->prepare($sql);
	    $stmt->bindValue(":id", isset($route[2]) ? $route[2] : null);
	    $stmt->execute();

	    if($stmt->rowCount() > 0)
	    {
	    	$row  = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $arr_json = array('status' => 200, '' => $row);
	    }else{
			return $arr_json = array('status' => 'Não foram encontrados resultados!');
	    }
	}
	function doInsertClients($data){
		
		$sql = 'CALL INSERTCLIENTS(:a,:b,:c,:d,:e,:f,:g,:h,:i,:j,:k,:l,:m,:n,:o,:p,:q,:r,:s,:t,:u,:v,:w,:x,:y,:z,@x);';
	    $stmt = $this->db->prepare($sql);
	    $stmt->bindValue(':a', $data["clients"]["nome"]);
		$stmt->bindValue(':b', $data["clients"]["data_nascimento"]);
		$stmt->bindValue(':c', $data["clients"]["sexo"]);
		$stmt->bindValue(':d', $data["clients"]["estado_civil"]);
		$stmt->bindValue(':e', $data["clients"]["nib"]);
		$stmt->bindValue(':f', $data["clients"]["nif"]);
		$stmt->bindValue(':g', $data["clients"]["filiacao_pai"]);
		$stmt->bindValue(':h', $data["clients"]["filiacao_mae"]);
		$stmt->bindValue(':i', $data["clients"]["contactos"]["telefone"]);
		$stmt->bindValue(':j', $data["clients"]["contactos"]["telefone_alternativo"]);
		$stmt->bindValue(':k', $data["clients"]["contactos"]["email"]);
		$stmt->bindValue(':l', $data["clients"]["contactos"]["caixa_postal"]);
		$stmt->bindValue(':m', null);
		$stmt->bindValue(':n', $data["clients"]["endereco"]["rua"]);
		$stmt->bindValue(':o', $data["clients"]["endereco"]["morada"]);
		$stmt->bindValue(':p', $data["clients"]["endereco"]["cidade"]);
		$stmt->bindValue(':q', $data["clients"]["naturalidade"]);
		$stmt->bindValue(':r', $data["clients"]["documentacao"]["pessoal"]["tipo"]);
		$stmt->bindValue(':s', $data["clients"]["documentacao"]["pessoal"]["numero"]);
		$stmt->bindValue(':t', $data["clients"]["documentacao"]["pessoal"]["data_emissao"]);
		$stmt->bindValue(':u', $data["clients"]["documentacao"]["pessoal"]["data_validade"]);
		$stmt->bindValue(':v', $data["clients"]["documentacao"]["automovel"]["numero"]);
		$stmt->bindValue(':w', $data["clients"]["documentacao"]["automovel"]["data_emissao"]);
		$stmt->bindValue(':x', $data["clients"]["documentacao"]["automovel"]["data_validade"]);
		$stmt->bindValue(':y', $data["clients"]["profissao"]);
		$stmt->bindValue(':z', $data["clients"]["renumeracao"]);
	    $stmt->execute();

	    if($stmt->rowCount() > 0)
	    {
			return $arr_json = array('status' =>$this->db->query("select @x msg;")->fetch()["msg"]);
	    }else{
			return $arr_json = array('status' => "Erro ao processar o pedido!");
	    }
		
	}
	function doInsertApolicies($data){
		
		$sql = 'CALL INSERTAPOLICES(:a,:b,:c,:d,:e,:f,:g,:h,:i,@x);';
	    $stmt = $this->db->prepare($sql);
	    $stmt->bindValue(':a', $data["apolices"]["tipo"]);
		$stmt->bindValue(':b', $data["apolices"]["numero"]);
		$stmt->bindValue(':c', $data["apolices"]["data_inicio"]);
		$stmt->bindValue(':d', $data["apolices"]["data_fim"]);
		$stmt->bindValue(':e', $data["apolices"]["veiculo"]);
		$stmt->bindValue(':f', $data["apolices"]["cliente"]);
		$stmt->bindValue(':g', $data["apolices"]["tipologia"]);
		$stmt->bindValue(':h', $data["apolices"]["desconto"]);
		$stmt->bindValue(':i', str_replace(',', '.', $data["apolices"]["valor"]));
	    $stmt->execute();

	    if($stmt->rowCount() > 0)
	    {
			return $arr_json = array('status' =>$this->db->query("select @x msg;")->fetch()["msg"]);
	    }else{
			return $arr_json = array('status' => "Erro ao processar o pedido!");
	    }
		
	}
	function doInsertSinistros($data){
		
		$sql = 'CALL INSERTSINISTROS(:a,:b,:c,:d,:e,:f,:g,:h,:i,:j,:k,:l,:m,@x);';
	    $stmt = $this->db->prepare($sql);
	    $stmt->bindValue(':a', $data["sinistros"]["data"]);
		$stmt->bindValue(':b', $data["sinistros"]["danos_materiais_veiculos"]);
		$stmt->bindValue(':c', $data["sinistros"]["danos_materiais_objectos"]);
		$stmt->bindValue(':d', $data["sinistros"]["ferimentos"]);
		$stmt->bindValue(':e', $data["sinistros"]["descricao"]);
		$stmt->bindValue(':f', $data["sinistros"]["rua"]);
		$stmt->bindValue(':g', $data["sinistros"]["morada"]);
		$stmt->bindValue(':h', $data["sinistros"]["cidade"]);
		$stmt->bindValue(':i', $data["sinistros"]["apolice"]);
		$stmt->bindValue(':j', $data["sinistros"]["observacoes_segurado"]);
		$stmt->bindValue(':k', $data["sinistros"]["tomador"]);
		$stmt->bindValue(':l', $data["sinistros"]["tomador_veiculo"]);
		$stmt->bindValue(':m', $data["sinistros"]["observacoes_tomador"]);
	    $stmt->execute();

	    if($stmt->rowCount() > 0)
	    {
			return $arr_json = array('status' =>$this->db->query("select @x msg;")->fetch()["msg"]);
	    }else{
			return $arr_json = array('status' => "Erro ao processar o pedido!");
	    }
		
	}
	function doInsertSinistroArquivos(){

		if(!empty($_FILES))
		{

		 $msgerror = null;
		 $count = count($_FILES['photo']);
			
         for($i=0;$i<$count;$i++) {

	         if( !empty( $_FILES['photo']['tmp_name'][$i] ) && is_uploaded_file( $_FILES['photo']['tmp_name'][$i] ) )
		     {
				
			   $file_temp =  $_FILES['photo']['tmp_name'][$i]; 
			   $file_name = $_FILES['photo']['name'][$i];
			   $file_type = $_FILES['photo']['type'][$i];
			   $file_error = $_FILES['photo']['error'][$i];
			   $file_size = $_FILES['photo']['size'][$i];
	
			   $directoryName = date("M_Y");
			   $path = 'upload/sinistros/'.$directoryName.''; //create directory
			   $Filepath = 'upload/sinistros/'.$directoryName.'/'; //move file
			   $dbPath = 'upload/sinistros/'.$directoryName.'/'; //save path
			   if(!is_dir($path)){mkdir( $path, 0777, true );}
			   $temp = explode(".", $file_name);
			   $newfilename = round(microtime(true))+$i . '.' . end($temp);
			   $ext = end($temp);
				
			   $sql = 'CALL INSERTSINISTROARQUIVOS(:a,:b,:c,:d,@x);';
	           $stmt = $this->db->prepare($sql);
	           $stmt->bindValue(':a', $_POST['sinistro']);
		       $stmt->bindValue(':b', $dbPath.$newfilename);
		       $stmt->bindValue(':c', $file_size);
		       $stmt->bindValue(':d', $file_type);
	           $stmt->execute();

			   if($this->db->query("select @x msg;")->fetch()["msg"]=="200"){
				  move_uploaded_file($file_temp, $Filepath . $newfilename);
			   }
			   if($stmt->rowCount() > 0)
			   {
				   $msgerror=$this->db->query("select @x msg;")->fetch()["msg"];
			   }else{
			      $msgerror = "Erro ao processar o pedido!";
			   }	   
			   
		    }
         }
        }
        
		return $arr_json = array('status' =>$msgerror);
	}
	function doDelete($data){

		$sql = 'CALL DELETEALL(:a,:b)';
	    $stmt = $this->db->prepare($sql);
	    $stmt->bindValue(":a", $data["delete"]["ENTIDADE"]);
		$stmt->bindValue(":b", $data["delete"]["ID"]);
	    $stmt->execute();
	    if($stmt->rowCount() > 0)
	    {
			return $arr_json = array('status' => 200);
	    }else{
			return $arr_json = array('status' => 200);
	    }
	}
	function validatedoInsertClients($data)
    {

		$error = false;
		$msgerror = null;
		$required = array('nome', 'data_nascimento', 'sexo', 'estado_civil', 'nif','filiacao_pai','filiacao_mae','profissao','renumeracao','naturalidade');
		$requiredcontacts = array('telefone', 'telefone_alternativo', 'email');
		$requiredaddresss = array('rua', 'morada', 'cidade');
		$requiredidentification = array('tipo', 'numero', 'data_emissao', 'data_validade');
		$requiredidentificationauto = array('numero', 'data_emissao', 'data_validade');

		if (!is_int($data["clients"]["estado_civil"])) {
			$msgerror = 'Estado civil não é um número válido!';
		}
		if (!is_int($data["clients"]["contactos"]["telefone"])) {
			$msgerror = 'Telefone não é um número válido!';
		}
		if (!is_int($data["clients"]["contactos"]["telefone_alternativo"])) {
			$msgerror = 'Telefone alternativo não é um número válido!';
		}
		if (!is_int($data["clients"]["endereco"]["cidade"])) {
			$msgerror = 'Cidade não é um número válido!';
		}
		if (!is_int($data["clients"]["naturalidade"])) {
			$msgerror = 'Naturalidade não é um número válido!';
		}
		if (!is_int($data["clients"]["profissao"])) {
			$msgerror = 'Profissão não é um número válido!';
		}
		if (!is_int($data["clients"]["documentacao"]["pessoal"]["tipo"])) {
			$msgerror = 'Tipo de Identificação pessoal não é um número válido!';
		}
		if(!self::validateDate($data["clients"]["data_nascimento"])){
			$msgerror = 'Data de Nascimento inválida!';
		   }
		if(!self::validateDate($data["clients"]["documentacao"]["pessoal"]["data_emissao"])){
			$msgerror = 'Data de Emissão do documento de identificação inválida!';
	    }
		if(!self::validateDate($data["clients"]["documentacao"]["pessoal"]["data_validade"])){
			$msgerror = 'Data de Validade do documento de identificação inválida!';
	    }
		if(!self::validateDate($data["clients"]["documentacao"]["automovel"]["data_emissao"])){
			$msgerror = 'Data de Emissão do documento automóvel inválida!';
	    }
		if(!self::validateDate($data["clients"]["documentacao"]["automovel"]["data_validade"])){
			$msgerror = 'Data de Validade do documento automóvel inválida!';
	    }
		if (!is_float($data["clients"]["renumeracao"])) {
			$msgerror = 'Renumeração não é um número válido!';
		}
        foreach($required as $field) {
         if(!isset($data["clients"][$field]) || empty($data["clients"][$field])) {
            $error = true;
          }
        }
	    foreach($requiredcontacts as $field) {
		if(!isset($data["clients"]["contactos"][$field]) || empty($data["clients"]["contactos"][$field])) {
		   $error = true;
		 }
	    }
	    foreach($requiredaddresss as $field) {
		if(!isset($data["clients"]["endereco"][$field]) || empty($data["clients"]["endereco"][$field])) {
		   $error = true;
		 }
	    }
	    foreach($requiredidentification as $field) {
		if(!isset($data["clients"]["documentacao"]["pessoal"][$field]) || empty($data["clients"]["documentacao"]["pessoal"][$field])) {
		   $error = true;
		 }
	    }
	    foreach($requiredidentificationauto as $field) {
		if(!isset($data["clients"]["documentacao"]["automovel"][$field]) || empty($data["clients"]["documentacao"]["automovel"][$field])) {
		   $error = true;
		 }
	    }
		if ($error) {
			$msgerror = 'Dados de inserção inválidos ou não preenchidos, por favor preencher os dados requisitados!';
		}
		return $msgerror;
    }  
	function validatedoInsertApolicies($data)
    {

		$error = false;
		$msgerror = null;
		$required = array('tipo', 'numero', 'data_inicio', 'data_fim', 'cliente','veiculo','tipologia','valor');//desconto
		
		if (!is_int($data["apolices"]["tipo"])) {
			$msgerror = 'Tipo de apolice não é um número válido!';
		}
		if(!self::validateDate($data["apolices"]["data_inicio"])) {
			$msgerror = 'Data de Inicio inválida!';;
		}
		if(!self::validateDate($data["apolices"]["data_fim"])) {
			$msgerror = 'Data de Fim inválida!';;
		}
		if (!is_int($data["apolices"]["cliente"])) {
			$msgerror = 'Cliente não é um número válido!';
		}
		if (!is_int($data["apolices"]["veiculo"])) {
			$msgerror = 'Veículo não é um número válido!';
		}
		if (!is_int($data["apolices"]["tipologia"])) {
			$msgerror = 'Tipologia não é um número válido!';
		}
		if (!is_numeric($data["apolices"]["desconto"])) {
			$msgerror = 'Desconto não é um número válido!';
		}
		//if (!is_float($data["apolices"]["valor"])) {
		//	$msgerror = 'Valor não é um número válido!';
		//}
        foreach($required as $field) {
         if(!isset($data["apolices"][$field]) || empty($data["apolices"][$field])) {
            $error = true;
          }
        }
		if ($error) {
			$msgerror = 'Dados de inserção inválidos ou não preenchidos, por favor preencher os dados requisitados!';
		}
		return $msgerror;
    }  
	function validatedoInsertSinistros($data)
    {

		$error = false;
		$msgerror = null;
		$required = array('data', 'danos_materiais_veiculos', 'danos_materiais_objectos', 'ferimentos', 'descricao','rua','morada','cidade','apolice','observacoes_segurado','tomador','tomador_veiculo','observacoes_tomador');
		$bool = array(1,2);

		if(!self::validateDate($data["sinistros"]["data"])) {
			$msgerror = 'Data inválida!';;
		}
		if (!in_array($data["sinistros"]["danos_materiais_veiculos"],$bool)) {
			$msgerror = 'Danos materiais de veiculos não é um número válido!';
		}
		if (!in_array($data["sinistros"]["danos_materiais_objectos"],$bool)) {
			$msgerror = 'Danos materiais de objectos não é um número válido!';
		}
		if (!in_array($data["sinistros"]["ferimentos"],$bool)) {
			$msgerror = 'Ferimentos não é um número válido!';
		}
		if (!is_int($data["sinistros"]["cidade"])) {
			$msgerror = 'Cidade não é um número válido!';
		}
		if (!is_int($data["sinistros"]["apolice"])) {
			$msgerror = 'Apolice do seguro não é um número válido!';
		}
		if (!is_int($data["sinistros"]["tomador"])) {
			$msgerror = 'Tomador do seguro não é um número válido!';
		}
		if (!is_int($data["sinistros"]["tomador_veiculo"])) {
			$msgerror = 'Veiculo do tomador do seguro não é um número válido!';
		}
        foreach($required as $field) {
         if(!isset($data["sinistros"][$field]) || empty($data["sinistros"][$field])) {
            $error = true;
          }
        }
		if ($error) {
			$msgerror = 'Dados de inserção inválidos ou não preenchidos, por favor preencher os dados requisitados!';
		}
		return $msgerror;
    }   
	function validatedoInsertSinistroArquivos()
    {
		$error = false;
		$msgerror = null;

		$count = count($_FILES['photo']);
			
         for($i=0;$i<$count;$i++) {

	         if( !empty( $_FILES['photo']['tmp_name'][$i] ) && is_uploaded_file( $_FILES['photo']['tmp_name'][$i] ) )
		     {
				
				$file_temp =  $_FILES['photo']['tmp_name'][$i]; 
				$file_type = $_FILES['photo']['type'][$i];
				$file_size = $_FILES['photo']['size'][$i];

				$maxsize    = 2097152; //2MB 
				$acceptable = array( // FILE TYPE
				'image/jpeg',
				'image/jpg',
				'image/png' );
	
				if($file_size == 0) {
					$msgerror='ATENÇÃO: o tamanho da imagem deve ser inferior a 2MB!';
				} 
				if($file_size >= $maxsize) {
					$msgerror='ATENÇÃO: o tamanho da imagem deve ser inferior a 2MB!';
				} 
				if(!in_array($file_type, $acceptable)) {
					$msgerror='ATENÇÃO: formato de imagem inválido!';
				}
		     }
         }
		 if (isset($_POST['sinistro'])) {
			if (!is_numeric($_POST['sinistro'])) {
				$msgerror = 'Sinistro não é um número válido!';
			}
		  }else{
			$msgerror = 'Valor do sinistro não definido!';
		 }
		return $msgerror;
    }  
	function validateDelete($data)
    {
		$error = false;
		$required = array('clients', 'apolices', 'sinistros');

        if(isset($data["delete"]["ENTIDADE"]) && !empty($data["delete"]["ENTIDADE"]) && isset($data["delete"]["ID"]) && !empty($data["delete"]["ID"])) {
			if(!in_array($data["delete"]["ENTIDADE"],$required)){
				$error = true;
			}
        }else{
			$error = true;
		}
		return $error;
    }  

	function validateDate($date, $format = 'Y-m-d')
    {
      $d = DateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
    }  
}
?>