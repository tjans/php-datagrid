<link rel="stylesheet" href="examples.css" />
<?php
	require 'datagrid2.php';
	require 'data.php';

	$grid = new DataGrid2();
	$grid
		-> dataSource($flavors)
		-> caption("Flavors!")
		-> setRowFunction("mygrid_row")
		-> stripe('itemRow', 'altRow')
		-> initAttributes(
			array(
				"id"=>"MyGrid",
				"border"=>"1",
				"cellpadding"=>"5"
			)
		);

		$output = $grid
			->setColumn('Name', 'Item Name', array("width"=>"100"))	// adding via quick-add method, most columns will work this way
			->setColumn('Type', 'Item Type', array("style"=>"text-align:center;"))
			->setColumn('Action', 'Actions')
			->build();

	DataGrid2::quickTable($flavors);

function mygrid_row($row)
{
	$record = $row->getRecord();

	$row->setAttr(
		array(
			'id'=>"row_" . $record['Id']
		)
	);

	$nameCell = $row->getCell('Name');

	if($nameCell->getValue() == "Banana")
	{
		$nameCell->setAttr('style', "background:yellow;color:brown;");

	}

	return $row;
}