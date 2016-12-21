<div class="events-row-wrapper">
  <h3 class="headline font-special fontsize-xxl fontweight-600 lh-inherit align-center transform-uppercase"><?php print $block_title; ?></h3>
  <div class="row-wrapper">
    <?php foreach ($items as $item): ?>
      <div class="post-wrapper">
        <div class="post">
          <div class="entry-image">
            <a <?php print $item['link_attrs']; ?> >
              <?php print wp_get_attachment_image($item['image'], 'post-thumbnail'); ?>
            </a>
          </div>
          <div class="entry-wrap">
            <div class="entry-title">
              <h2><a <?php print $item['link_attrs']; ?> ><?php print $item['title']; ?></a></h2>
            </div>
            <div class="entry-content">
              <?php print $item['desc']; ?>
            </div>
            <div class="entry-readmore-wrapper">
              <a <?php print $item['link_attrs']; ?> ><?php print $item['link_title']; ?>&#187;</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>