<?php

$title = wr___('Settings') . ' &mdash; ' . $this->plugin_title;
$parent_file = 'options-general.php';

?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<ul id="wordefinery-tabs">
<?php if (!isset($tabs[$tab])) $tab = key($tabs); ?>
<?php foreach ($tabs as $t=>$n) : ?>
<li class="<?php echo $t==$tab?'selected':''; ?>">
<a href="?page=<?php echo $plugin_page; ?>&tab=<?php echo $t; ?>"><?php echo $n;?></a>
</li>
<?php endforeach; ?>
</ul>