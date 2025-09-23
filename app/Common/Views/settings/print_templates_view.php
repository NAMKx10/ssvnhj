<!-- رأس الصفحة -->
<div class="page-header d-print-none">
    <div class="container-xl"><h2 class="page-title">إدارة قوالب الطباعة</h2></div>
</div>

<!-- محتوى الصفحة -->
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <form action="index.php?page=handle_print_template_update" method="POST">
                    <?php foreach ($templates as $template): ?>
                        <fieldset class="form-fieldset">
                            <legend><?= html($template['template_name']) ?></legend>
                            <input type="hidden" name="templates[<?= $template['id'] ?>][id]" value="<?= $template['id'] ?>">
                            <div class="row mb-3">
                                <div class="col"><label class="form-label">رأس - يمين</label><textarea name="templates[<?= $template['id'] ?>][header_right]" class="form-control" rows="3"><?= html($template['header_right']) ?></textarea></div>
                                <div class="col"><label class="form-label">رأس - وسط</label><textarea name="templates[<?= $template['id'] ?>][header_center]" class="form-control" rows="3"><?= html($template['header_center']) ?></textarea></div>
                                <div class="col"><label class="form-label">رأس - يسار</label><textarea name="templates[<?= $template['id'] ?>][header_left]" class="form-control" rows="3"><?= html($template['header_left']) ?></textarea></div>
                            </div>
                            <div class="row">
                                <div class="col"><label class="form-label">تذييل - يمين</label><textarea name="templates[<?= $template['id'] ?>][footer_right]" class="form-control" rows="3"><?= html($template['footer_right']) ?></textarea></div>
                                <div class="col"><label class="form-label">تذييل - وسط</label><textarea name="templates[<?= $template['id'] ?>][footer_center]" class="form-control" rows="3"><?= html($template['footer_center']) ?></textarea></div>
                                <div class="col"><label class="form-label">تذييل - يسار</label><textarea name="templates[<?= $template['id'] ?>][footer_left]" class="form-control" rows="3"><?= html($template['footer_left']) ?></textarea></div>
                            </div>
                        </fieldset>
                    <?php endforeach; ?>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">حفظ كل التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>