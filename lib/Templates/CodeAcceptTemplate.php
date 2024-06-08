<?php declare(strict_types = 1);

class CodeAcceptTemplate extends AbstractTemplate
{
	public bool $accept_ok;

	protected function out(): void
	{ ?>
		<div class="TD-cat">Reģistrācija</div>
		<? if($this->accept_ok) { ?>
			<div class="TD-content">
				<div class="List-item">E-pasts apstiptināts veiksmīgi!</div>
			</div>
		<? } else {?>
			<div class="TD-content">
				<div class="List-item  error-form">Diemžēl e-pastu neizdevās apstiprināt!</div>
				<div class="List-item">
					Varianti:<ol>
						<li>nokavēts 15 min. apstiprināšanas termiņš</li>
						<li>nepareizs vai izlietots kods</li>
						<li>e-pasts jau ir apstiprināts</li>
					</ol>
					<p>Ja kas, tad raksti uz <a href="mailto:info@truemetal.lv">info@truemetal.lv</a></p>
				</div>
			</div><?
		}
	}
}
