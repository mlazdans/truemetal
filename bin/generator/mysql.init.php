<?php declare(strict_types = 1);

use dqdp\DBA\driver\MySQL_PDO;
use dqdp\DBA\Types\MySQLConnectParams;
use dqdp\TODO;
use dqdp\TypeGenerator\AbstractTypeGenerator;
use dqdp\TypeGenerator\FieldInfoCollection;
use dqdp\TypeGenerator\FieldInfoType;
use dqdp\TypeGenerator\FieldType;

require_once("D:\\truemetal\\site\\include\\boot.php");

require_once("mysqllib.php");

class VPTypeGenerator extends AbstractTypeGenerator
{
	private MySQL_PDO $db;
	private $Fields;

	function __construct()
	{
		parent::__construct(...func_get_args());

		$params = new MySQLConnectParams(database: "truemetal", username: "root", charset: 'utf8mb4');
		$this->db = (new MySQL_PDO($params))->connect();

		if($this->is_relation)
		{
			if(!($Fields = mysql_get_relation_fields($this->db, $this->name))){
				throw new InvalidArgumentException("Relation $this->name not found");
				exit(-1);
			}
		} else {
			new TODO("handle procedure");
			// if(!($Fields = ibase_get_proc_fields($this->db, $this->name))){
			// 	throw new InvalidArgumentException("Procedure $this->name not found");
			// }
		}

		$this->Fields = $this->field_mapper($Fields);
	}

	function get_sequence_name(): ?string {
		return null;
	}

	function get_db(): MySQL_PDO
	{
		return $this->db;
	}

	function field2prop(string $name): string
	{
		return $name;
	}

	function self2class(): string
	{
		return name2prop($this->name);
	}

	function get_fields(): FieldInfoCollection
	{
		return $this->Fields;
	}

	function get_proc_args(): ?FieldInfoCollection
	{
		return null;
		// $procArgs = ibase_get_proc_args($this->db, $this->name);
		// return $this->field_mapper($procArgs);
	}

	function get_pk(): string|array|null
	{
		return mysql_get_pk($this->db, $this->name);
	}

	function get_output_folder(): string
	{
		return "D:\\truemetal\\site\\lib\\gen";
	}

	# TODO: abstract out
	private function field_mapper(array $Fields): FieldInfoCollection
	{
		$FieldMap = new FieldInfoCollection;

		foreach($Fields as $field)
		{
			$FI = new FieldInfoType();
			$field_type = $field->DATA_TYPE;

			$FI->name = $this->field2prop($field->COLUMN_NAME);

			$FI->nullable = $field->IS_NULLABLE === 'YES';
			$FI->nullflag = $FI->nullable ? "?" : "";

			$FI->readonly = $field->IS_GENERATED !== 'NEVER';

			// Default value for the column. From MariaDB 10.2.7, literals are
			// *quoted* to distinguish them from expressions. NULL means that the
			// column has no default. In MariaDB 10.2.6 and earlier, no quotes
			// were used for any type of default and NULL can either mean that
			// there is no default, or that the default column value is NULL.
			if($field->COLUMN_DEFAULT)
			{
				if($field->COLUMN_DEFAULT != 'NULL'){
					$FI->default = $field->COLUMN_DEFAULT;
				}
			}

			switch($field_type)
			{
				case 'tinyint':
				case 'smallint':
				case 'mediumint':
				case 'int':
				case 'bigint': // TODO: test bigint overflow. maybe string?
					$FI->type = FieldType::int;
					$FI->php_type = 'int';
					break;
				case 'decimal':
					$FI->type = FieldType::decimal;
					$FI->php_type = 'string';
					$FI->precision = $field->NUMERIC_PRECISION;
					$FI->scale = $field->NUMERIC_SCALE;
					break;
				case 'float':
				case 'double':
					$FI->type = FieldType::float;
					$FI->php_type = 'float';
					break;
				case 'date':
					$FI->type = FieldType::date;
					$FI->php_type = 'string';
					break;
				case 'time':
					$FI->type = FieldType::time;
					$FI->php_type = 'string';
					break;
				case 'datetime':
					$FI->type = FieldType::timestamp;
					$FI->php_type = 'string';
					break;
				case 'timestamp':
					$FI->type = FieldType::timestamp;
					$FI->php_type = 'string';
					break;
				case 'year':
					$FI->type = FieldType::int;
					$FI->php_type = 'int';
					break;
				case 'char':
					$FI->type = FieldType::char;
					$FI->php_type = 'string';
					$FI->len = $field->CHARACTER_MAXIMUM_LENGTH;
					break;
				case 'varchar':
				case 'varbinary':
					$FI->type = FieldType::varchar;
					$FI->php_type = 'string';
					$FI->len = $field->CHARACTER_MAXIMUM_LENGTH;
					break;
				case 'tinyblob':
				case 'blob':
				case 'mediumblob':
				case 'longblob':
					$FI->type = FieldType::blob;
					$FI->php_type = 'string';
					break;
				case 'tinytext':
				case 'text':
				case 'mediumtext':
				case 'longtext':
					$FI->type = FieldType::text;
					$FI->php_type = 'string';
					break;
				default:
					throw new Error("Unsupported type: $field_type");
			}

			$FieldMap[$field->COLUMN_NAME] = $FI;
		}

		return $FieldMap;
	}

}

return VPTypeGenerator::class;
