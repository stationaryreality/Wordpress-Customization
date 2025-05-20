<?php
/* 
Template Name: MailPoet Page
Description: Minimal full-width template for MailPoet confirmation, unsubscribe, manage, and success pages.
*/

get_header(); ?>

<style>
  .mailpoet-container {
    max-width: 600px;
    margin: 6rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  }
  .mailpoet-container h1, 
  .mailpoet-container h2,
  .mailpoet-container p {
    text-align: center;
  }
</style>

<main class="mailpoet-page">
  <div class="mailpoet-container">
    <?php
    while ( have_posts() ) : the_post();
      the_content();
    endwhile;
    ?>
  </div>
</main>

<?php get_footer(); ?>
