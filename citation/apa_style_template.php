<?php
/**
 * VENOM · APA Style Citation Template
 */
defined('INDEX_AUTH') OR die('Direct access not allowed!');

$author = isset($detail['authors']) ? $detail['authors'] : (isset($detail['author']) ? $detail['author'] : 'Unknown Author');
$year = isset($detail['publish_year']) ? $detail['publish_year'] : 'n.d.';
$title = isset($detail['title']) ? $detail['title'] : 'Unknown Title';
$publisher = isset($detail['publisher_name']) ? $detail['publisher_name'] : 'Unknown Publisher';

echo '<div style="font-family: serif; line-height: 1.6; padding: 16px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #7c3aed;">';
echo '<strong>APA Style (7th Edition):</strong><br>';
echo '<em>' . htmlspecialchars($author) . '</em> (' . htmlspecialchars($year) . '). ';
echo '<em>' . htmlspecialchars($title) . '</em>. ';
echo htmlspecialchars($publisher) . '.';
echo '</div>';
?>