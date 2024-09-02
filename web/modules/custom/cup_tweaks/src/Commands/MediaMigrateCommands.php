<?php

namespace Drupal\cup_tweaks\Commands;

use Drush\Commands\DrushCommands;
use Drush\Drush;
use Drupal\media\Entity\Media;

/**
 * A Drush commandfile for media migrate.
 *
 * @SuppressWarnings(PHPMD)
 */
class MediaMigrateCommands extends DrushCommands {

  /**
   * Change bundle on media entities.
   *
   * @command cu_media:migrateFile2Doc
   *
   * @usage drush cu_media:migrateFile2Doc
   *
   * @aliases cumf2d
   */
  public function migrateEmbedMedia() {
    $saveEntity = FALSE;
    $storage = \Drupal::entityTypeManager()->getStorage('media');
    $entity_ids = \Drupal::entityQuery('media')
      ->condition('bundle', 'file')
      ->accessCheck(FALSE)
      ->execute();
    foreach ($entity_ids as $entity_id) {
      $entity = $storage->load($entity_id);
      $new_entity = Media::create([
        'bundle' => 'document',
        'field_media_document' => $entity->field_media_file->getValue(),
        'name' => $entity->label(),
        'uid' => $entity->getOwnerId(),
        'langcode' => $entity->langcode,
        'status' => $entity->status,
      ]);
      $new_entity->save();
      $entity->delete();
      Drush::output()->writeln("Created media (document): {$new_entity->id()}\nDeleted media {$entity->id()}");
    }
  }
}
