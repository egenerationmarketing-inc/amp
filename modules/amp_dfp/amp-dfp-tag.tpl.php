<?php
/**
 * @file
 * Template for an amp-dfp-tag.
 *
 * Available variables:
 * - layout: The layout of the ad.
 * - height: The height of the ad.
 * - width: The width of the ad.
 * - slot: The DFP ad slot string.
 * - amp_ad_json: Other settings, such as targeting, encoded in json.
 * - sticky: If the tag is sticky.
 * - tag: The full Drupal DFP tag object
 *
 * @see template_preprocess_amp_ad()
 */
?>

<?php if ($sticky): ?><amp-sticky-ad layout="nodisplay"><?php endif; ?>

<amp-ad type="doubleclick"
        <?php if ($layout): ?>layout="<?php print $layout; ?>"<?php endif; ?>
        height="<?php print $height; ?>"
        width="<?php print $width; ?>"
        data-slot="<?php print $slot; ?>"
        json='<?php print $amp_ad_json; ?>'
  >
</amp-ad>
<?php if ($sticky): ?></amp-sticky-ad><?php endif; ?>
