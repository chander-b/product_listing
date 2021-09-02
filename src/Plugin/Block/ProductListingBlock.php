<?php

namespace Drupal\product_listing\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Drupal\Core\StreamWrapper\PublicStream;

/**
 * Provides a 'Product listing' Block.
 *
 * @Block(
 *   id = "produt_listing_block",
 *   admin_label = @Translation("Product Listing"),
 *   category = @Translation("Product Listing"),
 * )
 */
class ProductListingBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $renderer = new ImageRenderer(
      new RendererStyle(400),
      new ImagickImageBackEnd()
    );
    $writer = new Writer($renderer);
    $node = \Drupal::routeMatch()->getParameter('node');
    $node = Node::load($node->id());
    $writer->writeFile($node->get('field_qr_code_link')->uri, PublicStream::basePath() . '/qrcode_' . $node->id() .'.png');
    $product_list[$node->id()]['product_title'] = $node->getTitle();
    $product_list[$node->id()]['product_des'] = $node->body_value;
    $product_list[$node->id()]['product_image'] = $node->get('field_product_image')->entity->url();
    $product_list[$node->id()]['product_qr_link'] = 'public://qrcode_' . $node->id() .'.png';
    return [
      '#theme' => 'product_listing_block',
      '#product_list' => $product_list,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

}
