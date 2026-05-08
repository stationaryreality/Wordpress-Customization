<?php

$type_counts = $args['type_counts'] ?? [];

if (!$type_counts) return;
?>

<div class="cpt-filters" style="margin-bottom:2em;">

<button onclick="selectAll(true)">Select All</button>
<button onclick="selectAll(false)">Deselect All</button>

<div style="margin-top:1em; display:flex; flex-wrap:wrap; gap:12px;">

<?php foreach ($type_counts as $type => $count): ?>

<label>

<input type="checkbox"
       value="<?php echo esc_attr($type); ?>"
       checked>

<?php echo ucfirst($type); ?>
(<?php echo $count; ?>)

</label>

<?php endforeach; ?>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

  const checkboxes = document.querySelectorAll('.cpt-filters input[type="checkbox"]');
  const items = document.querySelectorAll('.cpt-clean-list li');

  function filterList() {

    const active = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.value);

    items.forEach(item => {

      const type = item.getAttribute('data-type');

      item.style.display =
        active.includes(type) ? '' : 'none';
    });
  }

  checkboxes.forEach(cb => {
    cb.addEventListener('change', filterList);
  });

  window.selectAll = function(state) {

    checkboxes.forEach(cb => cb.checked = state);

    filterList();
  };

});
</script>