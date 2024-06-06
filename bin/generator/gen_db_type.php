<?php declare(strict_types = 1);

use dqdp\TypeGenerator\AbstractTypeGenerator;

# (\{'(VP\$)([^']*)'\})
# VP_$3

function usage(){
	printf("\nUsage:\n");
	printf("    %s (-t <table/view> | -p proc_name) [-d] [-n namespace] [-k primary_key] <init.php>\n", $GLOBALS['argv'][0]);
	printf("\n");
	printf("-d                 debug\n");
	printf("-n namespace       add namespace\n");
	printf("-k primary_key     use as primary_key\n");
	printf("init.php           PHP script returning VPTypeGenerator class\n");
}

# Globals
$ItemName = $ClassName = "";
$isRelation = $isProc = false;
$FieldMap = [];
$Types = [];
$NamespaceUse = [
	'dqdp\PropertyInitTrait',
];

# Get opts
$opts = getopt("n:t:p:dk:", [], $rest);

if(empty($argv[$rest]) || (empty($opts['t']) && empty($opts['p']))){
	usage();
	return;
}
$DBA_init = $argv[$rest]??null;
$Namespace = $opts['n']??null;
if(isset($opts['t'])){
	$isRelation = true;
	$ItemName = $opts['t'];
} else {
	$isProc = true;
	$ItemName = $opts['p'];
}
$Debug = isset($opts['d']);
$PKOverride = $opts['k'] ?? null;

$generator_class = require_once($DBA_init);

/**
 * @var AbstractTypeGenerator $DBA
 * */
$DBA = new $generator_class($ItemName, $isRelation, $isProc, $Namespace);

$G = new CodeGen();

// $DB = $DBA->get_db();
// require_once("genlib.php");

$G->name = $ItemName;
$G->namespace = $Namespace;
$G->is_proc = $isProc;
$G->is_relation = $isRelation;
$G->class_name = $DBA->self2class();
$G->field_map = $DBA->get_fields();
$G->output_folder = $DBA->get_output_folder();

// $G->namespace = "uggabugga\dadad";
// $G->other_namespaces = ['test\test', 'test2\test2'];
// list($FieldsNamespaceUse, $Constructors, $InitFunctions) = process_fields($FieldMap);
// $NamespaceUse = array_merge($NamespaceUse, $FieldsNamespaceUse);

if(empty($PKOverride)){
	$G->PK = $DBA->get_pk();
} else {
	$G->PK = $PKOverride;
}

// print $G->generate_type();
// print $G->generate_type_trait();
// print $G->generate_collection();
// print $G->generate_entity_trait($DBA);

$G->save_type();
$G->save_type_trait();
$G->save_collection();
$G->save_entity_trait($DBA);
$G->save_entity();

// $saveDummyType = function() use ($Namespace, $ClassName, $FieldMap, $Debug) {
// 	global $OutputFolder;

// 	$directory = join_paths([$OutputFolder, $Namespace, "Types"]);

// 	if(!is_dir($directory)){
// 		mkdir(directory: $directory, recursive: true);
// 	}

// 	$fileName = $directory.DIRECTORY_SEPARATOR.$ClassName."Dummy.php";

// 	$data = generateDummyType($ClassName, $FieldMap);

// 	if($Debug){
// 		print $data;
// 	}

// 	file_put_contents($fileName, $data);
// };
