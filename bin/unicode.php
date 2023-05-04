<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

# Pārkonvertē LV burtus uz unicode hex notation. Priekš Sphinx

# http://rishida.net/scripts/uniview/conversion.php
//ĀČĒĢĪĶĻŅŌŖŠŪŽ
//U+0100 U+010C U+0112 U+0122 U+012A U+0136 U+013B U+0145 U+014C U+0156 U+0160 U+016A U+017D

//āčēģīķļņōŗšūž
//U+0101 U+010D U+0113 U+0123 U+012B U+0137 U+013C U+0146 U+014D U+0157 U+0161 U+016B U+017E

$a1 = split(" ", "U+0100 U+010C U+0112 U+0122 U+012A U+0136 U+013B U+0145 U+014C U+0156 U+0160 U+016A U+017D");
$a2 = split(" ", "U+0101 U+010D U+0113 U+0123 U+012B U+0137 U+013C U+0146 U+014D U+0157 U+0161 U+016B U+017E");

# Konvertācija no Ā->ā
foreach($a1 as $k=>$v)
{
	printf("%s->%s, ", $v, $a2[$k]);
	if(($k + 1) % 5 == 0)
		print "\\\n";
}
print "\\\n";

# Pieattačo mazo burtus
foreach($a2 as $k=>$v)
{
	printf("%s, ", $v);
	if(($k + 1) % 5 == 0)
		print "\\\n";
}

print "\n";

