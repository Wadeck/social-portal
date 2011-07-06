<?php

namespace core\form\fields;

use core\tools\Utils;

use core\tools\Linker;

use core\form\Field;
use InvalidArgumentException;
/** @UnTested */
class DependentDropDownField extends Field {
	// protected $value inherit from Field contains the value of the currently selected item
	protected $slavesLinker;
	protected $valuesDescriptions;
	protected $isMaster;
	protected $isSlave;
	protected $data;
	
	protected $selectId;
	
	/**
	 * @param string $identifier
	 * @param string $description Translated string that will be displayed as label
	 * @param array $valuesDescriptions associative array value => description (translated)
	 * @param mixed $defaultValue Same type of value
	 * @param Linker $master Use to set the master of that linker to this
	 * @param Linker $slave Use to add a slave to that linker
	 * @param array data contains the data that will be passed to the slaves
	 * @param array $constraints
	 */
	public function __construct($identifier, $description, array $valuesDescriptions, $defaultValue, Linker $master = null, Linker $slave = null, $data=null, array $constraints = array()) {
		parent::__construct( $identifier, $description, $defaultValue, 'select', $constraints );
		if($master){
			$master->setMaster($this);
			$this->slavesLinker = $master;
			$this->isMaster = true;
			if(null === $data){
				throw new InvalidArgumentException('Data is null but it is a master element');
			}
			$this->data = $data;
		}else{
			$this->isMaster = false;
		}
		if($slave){
			$slave->addSlave($this);
			$this->isSlave = true;
		}else{
			$this->isSlave = false;
		}
		$this->valuesDescriptions = $valuesDescriptions;
	}
	
	public function getSelectId(){
		return $this->identifier;
	}
	
	protected function isAcceptType($type) {
		return in_array( $type, array( 'select' ) );
	}
	
	public function displayAll() {
		// radio_field div is necessary for the reset button
		$this->form->addJavascriptFile('jquery.js');
		$this->form->addJavascriptFile('dependent_dropdown.js');
		
		$classes[] = 'dependent_dropdown_field';
		if($this->isMaster){
			if($this->isSlave){
				$classes[] = 'master';
				$classes[] = 'slave';
			}else{
				$classes[] = 'master_only';
			}
		}else{
			if($this->isSlave){
				$classes[] = 'slave_only';
			}
		}
		$classes = implode(' ', $classes);
		
		if($this->slavesLinker){
			$slaves = $this->slavesLinker->getSlaves();
			$slavesIds = array_map(function($s){
				if($s instanceof DependentDropDownField){
					return $s->getSelectId();
				}else{
					return false;
				}
			}, $slaves);
			$slavesIdsJS = Utils::php2js($slavesIds);
			
			$data = $this->data;
			$dataJS = Utils::php2js($data);
		}
	?>
		<div class="<?php echo $classes;?>">
			<div class="label_error"><?php
			$this->insertLabel();
			$this->insertErrorMessage();
			?></div>
			<select name="<?php echo $this->identifier; ?>"
				id="<?php echo $this->identifier; ?>"
				class="dependent_selected_<?php echo $this->value; ?>"
				<?php if($this->slavesLinker): ?>
					onChange="onSelectChange(this, <?php echo $slavesIdsJS; ?>, <?php echo $dataJS; ?>);"
				<?php endif; ?>
				><?php
				foreach($this->valuesDescriptions as $key => $value){
					$this->insertOption( $key, $value );
				}
				?>
			</select>
		</div>
	<?php
	}
	protected function insertOption($key, $description) {
		?>
		<option
			class="<?php echo $this->getConstraintsAsString(); ?>"
			value="<?php echo $key; ?>"
		<?php // to determine the initial value
			if( $key == $this->value ) echo 'selected'; ?>>
		<?php echo $description; ?>
			</option>
<?php
	}
	public function insertField() {}
}