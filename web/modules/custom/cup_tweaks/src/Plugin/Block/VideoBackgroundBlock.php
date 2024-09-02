<?php

namespace Drupal\cup_tweaks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Entity\ImageStyle;

/**
 * Provides a Video Background block.
 *
 * @Block(
 *   id = "cup_video_background_block",
 *   admin_label = @Translation("CUP Video background block"),
 * )
 */
class VideoBackGroundBlock extends BlockBase {

    /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['video'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Video file'),
      '#description' => $this->t('Upload an mp4 video'),  
      '#default_value' => isset($config['video']) ? $config['video'] : '',
      '#upload_location' => 'public://videos',
      '#upload_validators' => [
        'file_validate_extensions' => ['mp4'],
      ],        
    ];

    $form['background'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Background image'),
      '#description' => $this->t('Upload a background image file to show if video is not playable.'),  
      '#default_value' => isset($config['background']) ? $config['background'] : '',
      '#upload_location' => 'public://videos',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg gif'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $form_file = $form_state->getValue('video', 0);
    if (isset($form_file[0]) && !empty($form_file[0])) {
      $file = File::load($form_file[0]);
      $file->setPermanent();
      $file->save();
    }
    $form_file = $form_state->getValue('background', 0);
    if (isset($form_file[0]) && !empty($form_file[0])) {
      $file = File::load($form_file[0]);
      $file->setPermanent();
      $file->save();
    }
    $this->setConfigurationValue('video', $form_state->getValue('video'));
    $this->setConfigurationValue('background', $form_state->getValue('background'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    if (!empty($config['video'][0])) {
      $video_file = File::load($config['video'][0]);
      if (!empty($video_file)) {
        $video_url = $video_file->url();
      }
    }
    if (!empty($config['background'][0])) {
      $image_file = File::load($config['background'][0]);
      if (!empty($image_file)) {
        $image_uri = $image_file->getFileUri();
        $style_wide = ImageStyle::load('video_background_wide');
        $style_narrow = ImageStyle::load('video_background_narrow');
        $wide_url = $style_wide->buildUrl($image_uri);
        $narrow_url = $style_narrow->buildUrl($image_uri);
      }
    }
    return array(
      '#theme' => 'cup_video_background',
      '#video_url' => !empty($video_url) ? $video_url : '',
      '#id' => 'cup-video',
      '#bg_img_url_wide' => !empty($wide_url) ? $wide_url : '',
      '#bg_img_url_narrow' => !empty($narrow_url) ? $narrow_url : '',
    );
  }

}
