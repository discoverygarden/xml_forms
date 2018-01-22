<?php

/**
 * @file
 * Handles schema caching so that they don't need to be downloaded each request.
 */

use Drupal\Core\Cache\Cache;

/**
 * Stores/retrieves schema documents from the file location on disk.
 *
 * The mappings of urls to actual files are stored in the Drupal cache.
 *
 * @codingStandardsIgnoreStart
 * XXX: Poorly implemented sniff indicates class should begin with
 * "XMLSchemaApi.info" instead of just "XMLSchemaApi"... ideally, this
 * should move somewhere under the Drupal\xml_schema_api namespace at
 * somepoint... because of this, we've left it with only the original
 * prefix, to avoid having to carry through the change.
 */
class XmlSchemaCache {
// @codingStandardsIgnoreEnd

  /**
   * The Drupal cache ID where the mappings of urls to actual files are stored.
   */
  const ID = 'xml-schema-table';
  const TYPE = 'data';

  const LOCATION = 'public://schemas';

  // @deprecated Constants
  // @codingStandardsIgnoreStart
  const Type = self::TYPE;
  // @codingStandardsIgnoreEnd

  /**
   * Checks if the cache exists.
   *
   * @return bool
   *   TRUE if the cache exists; FALSE otherwise.
   */
  public static function exists() {
    return \Drupal::cache(static::TYPE)->get(static::ID) && static::locationExists();
  }

  /**
   * Creates the cache if it does not exist.
   *
   * @return bool
   *   TRUE if the cache was created or already exists; FALSE otherwise.
   */
  public static function create() {
    if (!static::exists()) {
      // Clear will initialize the table and files location.
      return static::clear();
    }
    return TRUE;
  }

  /**
   * Clears the cache table and removes the cached files.
   *
   * @return bool
   *   TRUE if the cache was successfully cleared or initialized; FALSE if it
   *   fails.
   */
  public static function clear() {
    // Assumed to always work.
    static::clearCacheTable();
    return static::clearCachedFiles();
  }

  /**
   * Clears the Cache table.
   */
  protected static function clearCacheTable() {
    // Empty to start with.
    \Drupal::cache(static::TYPE)->set(static::ID, [], Cache::PERMANENT);
  }

  /**
   * Removes the Cached files if they exist.
   *
   * Creates the cached file location if it doesn't exist.
   *
   * @return bool
   *   TRUE if the cached files were successfully cleared, or if the cached file
   *   location was successfully created.
   */
  protected static function clearCachedFiles() {
    if (static::locationExists()) {
      $files = scandir(static::LOCATION);
      foreach ($files as $file) {
        if ($file != "." && $file != "..") {
          $full_filename = static::LOCATION . "/" . $file;
          if (filetype($full_filename) != "dir") {
            unlink($full_filename);
          }
        }
      }
      return TRUE;
    }
    return @mkdir(static::LOCATION, 0775, FALSE);
  }

  /**
   * Checks to see if the cache location exists.
   *
   * @param string $filename
   *   The name of the cached file to retrieve.
   *
   * @return bool
   *   TRUE if the cache exists; FALSE otherwise. Hmm ... this comment was also
   *   unfinished.
   */
  public static function locationExists($filename = NULL) {
    $filename = empty($filename) ? static::LOCATION : $filename;
    return file_exists($filename);
  }

  /**
   * Get the cached document.
   *
   * @param string $filename
   *   The filename used by schemas internally to refer to specific files. May
   *   also be a url.
   *
   * @return DOMDocument
   *   The cached DOMDocument if found; FALSE otherwise.
   */
  public static function get($filename) {
    $cached_filename = static::getCachedFilename($filename);
    if ($cached_filename !== FALSE) {
      $doc = new DOMDocument();
      if (!@$doc->load($cached_filename)) {
        // Failed to load clear instance from the cache.
        $cache = \Drupal::cache(static::TYPE)->get(static::ID)->data;
        unset($cache[$filename]);
        \Drupal::cache(static::TYPE)->set(static::ID, $cache);
        return FALSE;
      }
      // Success.
      return $doc;
    }
    return FALSE;
  }

  /**
   * Gets the cached document's filename.
   *
   * @param string $filename
   *   The filename used by schemas internally to refer to specific files. May
   *   also be a url.
   *
   * @return string
   *   The location of the cached if found; FALSE otherwise.
   */
  public static function getCachedFilename($filename) {
    // Only Creates if none existed.
    if (static::create()) {
      $cache = \Drupal::cache(static::TYPE)->get(static::ID)->data;
      if (isset($filename) && isset($cache[$filename])) {
        if (static::locationExists($cache[$filename])) {
          return $cache[$filename];
        }
        // Failed to find the file clear instance from the cache.
        unset($cache[$filename]);
        \Drupal::cache(static::TYPE)->set(static::ID, $cache);
      }
    }
    return FALSE;
  }

  /**
   * Caches the given document with the given filename.
   *
   * @param string $filename
   *   The name of the schema file.
   * @param DOMDocument $doc
   *   The schema file.
   */
  public static function set($filename, DOMDocument $doc) {
    // Only Creates if none existed.
    if (self::create()) {
      $cached_filename = file_create_filename('schema.xsd', static::LOCATION);
      if (@$doc->save($cached_filename) != FALSE) {
        $cache = \Drupal::cache(static::TYPE)->get(static::ID)->data;
        $cache[$filename] = $cached_filename;
        \Drupal::cache(static::TYPE)->set(static::ID, $cache);
      }
    }
  }

}
