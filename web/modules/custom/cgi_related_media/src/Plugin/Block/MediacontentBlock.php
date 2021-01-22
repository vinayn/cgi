<?php

namespace Drupal\cgi_related_media\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'MediacontentBlock' block.
 *
 * @Block(
 *  id = "related_media_content",
 *  admin_label = @Translation("Related Media content"),
 * )
 */
class MediacontentBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	  
	  
	//initiat variable
	
	$build = [];
	  

	$dontshowpreviousrec = [];


	
	// Media content first record - Media announcement
	
	$build['#mediaAnnouncementTaxonomynid'] = $this->getrecordbytaxonomyterm(1,$dontshowpreviousrec);
	
	
	// Media content second record - Brochure
	$build['#brouchureTaxonomynid'] = $this->getrecordbytaxonomyterm(2,$dontshowpreviousrec);
	
	// Media content third record - whitepaper
	$build['#whitepaperTaxonomynid'] = $this->getrecordbytaxonomyterm(3,$dontshowpreviousrec);


	// All record

	$query = \Drupal::entityQuery('node')
	->condition('type', 'cgi_media')
	->condition('status', NODE_PUBLISHED)
	->range(0, 6)
	->sort('created' , 'DESC');




	// Add condition for restricting previous records
	if(!empty($dontshowpreviousrec))
	{


		$query->condition('nid', $dontshowpreviousrec,'NOT IN');

		

	}	
	
		// Debug.
	//dump($query->__toString());



	$remainingrecords = $query->execute();
	
	if(!empty($remainingrecords))
	{
		
		
		
		
		foreach($remainingrecords as $eachremainingrecords)
		{
			
			$nodeEachremainingrecords =  \Drupal::entityTypeManager()->getStorage('node')->load($eachremainingrecords);
			
							//get term title
				
				$termObjects = $nodeEachremainingrecords->get('field_media_category')->referencedEntities();
			
				if(!empty($termObjects))
				{	
			
					foreach($termObjects as $eachtermObject)
					{
						$mediaCategory[] = $eachtermObject->getName();
						
					}	
	
				
				}
			
			$build['#remainingrecords'][] = [
											'title' => $nodeEachremainingrecords->get('title')->value ,
											
											
											'field_media_category' => $mediaCategory,
											'body' => $nodeEachremainingrecords->get('body')->value ,
											'url' => \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$eachremainingrecords)		
															
											
											];
											
											
				if($nodeEachremainingrecords->field_image->entity )
				{

					$build['#remainingrecords'] ['field_image'] = [
														'url' => $nodeEachremainingrecords->field_image->entity->uri->value,
														'alt' => $nodeEachremainingrecords->field_image->alt,
														];

				}		
			
			
		}	
		
		
	}
	
	

	  
	//node_list
	
	$build['#theme'] = 'related_media_content';
	$build['#cache'] = [
	 
		 'tags' => ['node_list:cgi_media'],

		
	];
	
	
    return $build;
  }
  


		public function getrecordbytaxonomyterm($termid,&$dontshowpreviousrec)
		{
			
			
			
						// Media content first record Media announcement

				$query = \Drupal::entityQuery('node')
				->condition('type', 'cgi_media')
				->condition('status', NODE_PUBLISHED)
				->condition('field_media_category', $termid,'IN')
				->range(0, 1)
				->sort('created' , 'DESC');

				$Taxonomyconditionnid  = $query->execute();
				
				
				
			if(!empty($Taxonomyconditionnid))
			{

				$Taxonomyconditionnid = array_values($Taxonomyconditionnid);
				
				$node = \Drupal::entityTypeManager()->getStorage('node')->load($Taxonomyconditionnid[0]);
				
				
				//get term title
				
				$termObjects = $node->get('field_media_category')->referencedEntities();
			
				if(!empty($termObjects))
				{	
			
					foreach($termObjects as $eachtermObject)
					{
						$mediaCategory[] = $eachtermObject->getName();
						
					}	
	
				
				}
				
				
				// assignn to twig variable
				$twigvariabledata['data'] = [
												'title' => $node->get('title')->value ,

												'field_media_category' => $mediaCategory,
												'url' => \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$Taxonomyconditionnid[0])
												
												];
												
				if($node->field_image->entity )
				{

					$twigvariabledata['data']['field_image'] = [
																'url' => $node->field_image->entity->uri->value,
																'alt' => $node->field_image->alt,
																];

				}


				// show body content for whitepaper
				if($termid == 3)
				{
					$twigvariabledata['data']['body'] = $node->get('body')->value;

				}
															
				$dontshowpreviousrec[] = $Taxonomyconditionnid[0];
				
			
				return $twigvariabledata['data'];

			}
			
			
			
		}	

}
