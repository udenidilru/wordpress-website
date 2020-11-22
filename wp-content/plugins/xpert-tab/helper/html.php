<?php

abstract class Htmltab {

	public static function getMediaButtion()
	{
		$html = '<a href="#" id="xt-media" class="btn button" data-toggle="modal" data-target="#xt-modal">XpertTabs</a>';

		return $html;
	}

	public static function getModal()
	{
		$html = array();

		$html[] = '<div class="modal fade" id="xt-modal" data-backdrop="static">';
		$html[] = '<div class="modal-dialog">';
		$html[] = '<div class="modal-content">';
		$html[] = self::getModalHeader();
		$html[] = '<div class="modal-body">';
		$html[] = '<button type="button" class="tx-btn tx-btn-success action-new-tab">Add New</button>';
		$html[] = self::getPresetStyles();
		$html[] = self::getRepeatable();
		$html[] = '</div> <!-- Modal Body End -->';
		$html[] = self::getModalFooter();
		$html[] = '</div>';
		$html[] = '</div> <!-- Modal Dialog End -->';
		$html[] = '</div> <!-- Modal End -->';

		return implode('', $html);
	}

	public static function getModalHeader()
	{
		$html = '<div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="xa-modal-label">Xpert Tabs</h4>
			      </div>';

		return $html;
	}

	public static function getPresetStyles(){
		$html = '<select class="pull-right presets-tab uninit" >
					<option value="xa-default" selected >Select Your Styles</option>
					<optgroup label="TX Tabs Styles">
						<option value="xa-default">TX DEFAULT</option>
						<option value="xa-green">TX GREEN</option>
					  	<option value="xa-blue">TX BLUE</option>
					  	<option value="xa-yellow" >TX YELLOW</option>
					  	<option value="xa-red" >TX RED</option>
				  	</optgroup>
				</select>';

		return $html;
	}

	public static function getRepeatable()
	{
		$html = '<div id="repeatable-tab" class="panel-group xa-default" role="tablist"><br>
				<div class="panel panel-tab panel-default clonable-tab">
					<div class="panel-heading" role="tab" id="collapseListGroupHeading1">
						<h4 class="panel-title">
						  <a class="collapsed" data-toggle="collapse"  data-parent="#repeatable-tab" href="#tab-1" aria-expanded="false" aria-controls="tab-1">
							<span class="fa fa fa-bars action-drag"></span>
							<i id="title-icon"></i>
							<span class="tx-title">New Item</span>
						  </a>
						  <span class="btn fa fa-times action-remove-tab pull-right"></span>
						</h4>
					</div>
					  <div id="tab-1" class="panel-collapse tab-collapse collapse" role="tabpanel">
					      <div class="panel-body tx-form">
					       	  	<form class="tx-form">
									<div class="width-70">
										<label for="title">Title</label>
										<input type="text" class="title-tab" placeholder="Enter title">
									</div>
									<div class="width-30">
										<label for="icon">Icon</label>
										' . self::getIconLists() . '
									</div>
									<div class="width-100">
										<label for="content">Content</label>
										<textarea rows="10" name="content" class="content-tab"></textarea>
									</div>
								</form>
					     </div>
					 </div>
				</div>
			</div>';
		return $html;
	}

	public static function getModalFooter()
	{
		$html = ' <div class="modal-footer">
			        <button type="button" class="tx-btn tx-btn-primary pull-left action-insert-tab">Insert</button>
			        <button type="button" class="tx-btn tx-btn-danger" data-dismiss="modal">Close</button>
			      </div>';
	    return $html;
	}

	public static function getIconLists()
	{
		$html = array();

		$icons = include dirname(__FILE__) . '/icons.php';

		$html[] = '<select class="icons" style="font-family: \'FontAwesome\'">';
			foreach ($icons['fontawesome'] as $icon => $content )
			{
				$html[] = '<option class="fa fa-'.$icon.'" value="'. $icon .'"> ' .$icon .'</option>';
			}
		$html[] = '</select>';

		return implode('', $html);
	}
}
