<?

interface SpiderResource
{
	public function query();
	public function queryNew($latest);
	public function fetch();
	public function navigate($url);
}

?>
