<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="main-content-inner">
	<div class="row">
		<div class="col-12 mt-5">
			<div class="card">
				<div class="card-body">
					<div class="invoice-head">
						<div class="row">
							<div class="iv-left col-12">
								<span>Sosyal Medya Ayarları</span>
							</div>
						</div>
					</div>
					<form action="" method="post" class="ajax-form-json">
						<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>

						<div class="form-group">
							<?php echo form_label('Sosyal Medya Durumu<small class="text-danger float-right">'.form_error('sosyal_medya').'</small>', 'sosyal_medya', ['style'=>'width: 100%;']);?>
							<?php echo form_dropdown('sosyal_medya', [1=>'Açık', 0=>'Kapalı'], html_entity_decode(htmlspecialchars_decode(@$this->config->item('sosyal_medya'),ENT_QUOTES),ENT_QUOTES,'UTF-8'), ['class'=>'form-control', 'autocomplete'=>'off'])?>
						</div>					
						 
						<button class="btn btn-primary" type="submit">Kaydet</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>