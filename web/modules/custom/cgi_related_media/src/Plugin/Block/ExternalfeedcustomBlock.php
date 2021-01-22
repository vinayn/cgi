<?php

namespace Drupal\cgi_related_media\Plugin\Block;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ExternalfeedcustomBlock' block.
 *
 * @Block(
 *  id = "externalfeedcustom",
 *  admin_label = @Translation("External feed"),
 * )
 */
class ExternalfeedcustomBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
	
	try {
	
		$host = \Drupal::request()->getSchemeAndHttpHost();
		
		 $client = \Drupal::httpClient();

		$response = $client->get($host.'/blog_feed.json');
		

		$blogfeeds = json_decode($response->getBody(), TRUE);


		 $build['#theme'] = 'externalfeedcustom';
		 $build['#feeddata'] = $blogfeeds['blogs'];
	}
	catch (GuzzleException   $exception) {
     \Drupal::logger('cgi_related_media')->error($exception->getMessage());
    }	
	catch (RequestException $exception) {
      \Drupal::logger('cgi_related_media')->error($exception->getMessage());
    }	
	
	
	//cachge for  6 hours
	$build['#cache']['max-age'] = 21600;

    return $build;
  }

}
