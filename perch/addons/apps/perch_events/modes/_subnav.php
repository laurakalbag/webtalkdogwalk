<?php

	echo $HTML->subnav($CurrentUser, array(
		array('page'=>array(
					'perch_events',
					'perch_events/delete',
					'perch_events/edit'
			), 'label'=>'Add/Edit'),
		array('page'=>array(
					'perch_events/categories',
					'perch_events/categories/edit',
					'perch_events/categories/delete',
					'perch_events/categories/new'

			), 'label'=>'Categories', 'priv'=>'perch_events.categories.manage')
	));
?>