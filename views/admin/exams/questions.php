<?php
// app/views/admin/exams/questions.php
$basePath = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }
?>

<div class="max-w-6xl mx-auto">
    <!-- Exam Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900">Assessment questions</h2>
                <p class="text-sm text-gray-600 mt-1">Assessment: <span class="font-semibold text-gray-900"><?= htmlspecialchars($exam['title'] ?? '') ?></span></p>
            </div>
            <a href="<?= $basePath ?>/admin/exams" class="text-sm text-gray-600 hover:text-gray-900">Back to list of assessments</a>
        </div>
    </div>

    <!-- Questions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="ri-question-line text-blue-600 text-xl"></i>
                </div>
                <h2 class="text-lg font-medium text-gray-900">Assessment questions</h2>
                <button type="button" id="helpBtn" class="mr-2 text-blue-600" title="directions"><i class="ri-question-line"></i></button>
            </div>
            <button type="button" id="addQuestionBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center">
                <i class="ri-add-line text-lg mr-2"></i>
                Add Question
            </button>
        </div>

        <!-- Creating a new question identical to the design -->
        <div id="newQuestionCard" class="hidden border border-gray-200 rounded-xl p-5 mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">New question</h3>
                <button type="button" id="removeDraftBtn" class="text-red-600 hover:text-red-700" title="closing"><i class="ri-delete-bin-5-line"></i></button>
            </div>

            <form method="post" action="<?= $basePath ?>/admin/exams/questions" class="space-y-5">
                <input type="hidden" name="exam_id" value="<?= (int)($exam['id'] ?? 0) ?>">
                <input type="hidden" name="order_index" value="<?= count($questions ?? []) ?>">

                <!-- Question text -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Question text</label>
                    <textarea name="question_text" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Write the question here ... " required></textarea>
                </div>

                <!-- Question type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Question type</label>
                    <select name="question_type" id="qType" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="text" selected>Text answer</option>
                        <option value="mcq">Multiple choice</option>
                        <option value="truefalse">Right/error</option>
                    </select>
                </div>

                <!-- Typical answer to the text -->
                <div id="textWrap">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Typical answer</label>
                    <textarea name="correct_answer" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Write the typical answer here ... "></textarea>
                    <p class="text-xs text-gray-500 mt-2">Text answers will be manually reviewed</p>
                </div>

                <!-- Multiple selection options -->
                <div id="mcqWrap" class="hidden">
                    <div class="space-y-3">
                        <?php for ($i=0; $i<4; $i++): $idx=$i+1; ?>
                        <div class="flex items-center">
                            <input type="text" name="option_text[]" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Choice <?= $idx ?>">
                            <input type="radio" name="correct_index" value="<?= $idx ?>" class="mr-3 w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" <?= $idx===1?'checked':''; ?>>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Choose the correct answer by clicking on the corresponding circle</p>
                    <input type="hidden" name="correct_answer" id="mcqCorrectAnswer" value="">
                </div>

                <!-- Right/error - -->
                <div id="tfWrap" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">The correct answer (correct/error)</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input type="radio" name="correct_answer" value="Right " class="mr-2 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                            Right
                        </label>
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input type="radio" name="correct_answer" value="mistake" class="mr-2 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            mistake
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" id="cancelAdd" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">cancellation</button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Save the question</button>
                </div>
            </form>
        </div>

        <!-- Current questions list (CRUD As) - - -->
        <div id="questionsContainer">
            <?php if (!empty($questions)): ?>
                <?php foreach ($questions as $index => $q): ?>
                    <div class="question-item border border-gray-200 rounded-xl p-5 mb-4" data-question-id="<?= (int)$q['id'] ?>">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-900">Question <?= $index+1 ?></h4>
                            <form method="post" action="<?= $basePath ?>/admin/exams/questions/delete" onsubmit="return confirm('Do you want to delete this question?');">
                                <input type="hidden" name="exam_id" value="<?= (int)($exam['id'] ?? 0) ?>">
                                <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                                <button class="text-red-600 hover:text-red-700" title="Delete"><i class="ri-delete-bin-line"></i></button>
                            </form>
                        </div>

                        <!-- The text of the question (editing) - -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Question text</label>
                            <form method="post" action="<?= $basePath ?>/admin/exams/questions/update" class="flex items-center gap-3">
                                <input type="hidden" name="exam_id" value="<?= (int)($exam['id'] ?? 0) ?>">
                                <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                                <textarea name="question_text" rows="3" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($q['question_text'] ?? '') ?></textarea>
                                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">Preservation</button>
                            </form>
                        </div>

                        <?php if (($q['question_type'] ?? '') !== 'text'): ?>
                        <!-- Question options (if any) - -->
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Choices</label>
                            <ul class="space-y-2">
                                <?php foreach (($optionsByQ[$q['id']] ?? []) as $op): ?>
                                <li class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                                    <form method="post" action="<?= $basePath ?>/admin/exams/options/update" class="flex items-center gap-2 flex-1">
                                        <input type="hidden" name="exam_id" value="<?= (int)($exam['id'] ?? 0) ?>">
                                        <input type="hidden" name="option_id" value="<?= (int)$op['id'] ?>">
                                        <input type="text" name="option_text" value="<?= htmlspecialchars($op['option_text'] ?? '') ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <button class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs">Preservation</button>
                                    </form>
                                    <div class="flex items-center gap-2">
                                        <?php if ((int)$op['is_correct'] !== 1): ?>
                                        <form method="post" action="<?= $basePath ?>/admin/exams/options/set-correct">
                                            <input type="hidden" name="exam_id" value="<?= (int)($exam['id'] ?? 0) ?>">
                                            <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                                            <input type="hidden" name="option_id" value="<?= (int)$op['id'] ?>">
                                            <button class="px-3 py-1.5 text-green-700 hover:text-green-800 bg-green-50 hover:bg-green-100 rounded-lg text-xs">Mark as correct</button>
                                        </form>
                                        <?php else: ?>
                                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">Right</span>
                                        <?php endif; ?>
                                        <form method="post" action="<?= $basePath ?>/admin/exams/options/delete" onsubmit="return confirm('Delete this option?');">
                                            <input type="hidden" name="exam_id" value="<?= (int)($exam['id'] ?? 0) ?>">
                                            <input type="hidden" name="option_id" value="<?= (int)$op['id'] ?>">
                                            <button class="px-3 py-1.5 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg text-xs">Remove</button>
                                        </form>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>

                            <!-- Add a new option -->
                            <form method="post" action="<?= $basePath ?>/admin/exams/options" class="mt-3 flex items-end gap-3">
                                <input type="hidden" name="exam_id" value="<?= (int)($exam['id'] ?? 0) ?>">
                                <input type="hidden" name="question_id" value="<?= (int)$q['id'] ?>">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">New option</label>
                                    <input type="text" name="option_text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="The text of the option ">
                                </div>
                                <label class="inline-flex items-center text-sm text-gray-700">
                                    <input type="checkbox" name="is_correct" class="mr-2 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    Right?
                                </label>
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Add an option</button>
                            </form>
                        </div>
                        <?php else: ?>
                        <p class="text-xs text-gray-500">Question with a text.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="noQuestions" class="text-center py-12 text-gray-500 <?= !empty($questions) ? 'hidden' : '' ?>">
            <i class="ri-question-mark text-4xl mb-4"></i>
            <p>No questions have yet been added</p>
            <p class="text-sm">Click "Add Question" to start creating the assessment</p>
        </div>
    </div>
</div>

<script>
    // Open/close the construction card
    const addBtn = document.getElementById('addQuestionBtn');
    const newCard = document.getElementById('newQuestionCard');
    const cancelAdd = document.getElementById('cancelAdd');
    const removeDraftBtn = document.getElementById('removeDraftBtn');
    function hideDraft(){ if(newCard) newCard.classList.add('hidden'); }
    if (addBtn && newCard) addBtn.addEventListener('click', ()=> newCard.classList.toggle('hidden'));
    if (cancelAdd) cancelAdd.addEventListener('click', hideDraft);
    if (removeDraftBtn) removeDraftBtn.addEventListener('click', hideDraft);

    // Show the sections according to the type of question
    const qType = document.getElementById('qType');
    const tfWrap = document.getElementById('tfWrap');
    const mcqWrap = document.getElementById('mcqWrap');
    const textWrap = document.getElementById('textWrap');
    const mcqCorrectAnswer = document.getElementById('mcqCorrectAnswer');
    
    function syncType(){
        const v = qType ? qType.value : 'text';
        if (tfWrap) tfWrap.classList.toggle('hidden', v !== 'truefalse');
        if (mcqWrap) mcqWrap.classList.toggle('hidden', v !== 'mcq');
        if (textWrap) textWrap.classList.toggle('hidden', v !== 'text');
    }
    if (qType) { qType.addEventListener('change', syncType); syncType(); }

    // Treating the correct answer for multiple selection
    if (mcqWrap) {
        mcqWrap.addEventListener('change', function(e) {
            if (e.target.name === 'correct_index') {
                const selectedIndex = parseInt(e.target.value);
                const optionInputs = mcqWrap.querySelectorAll('input[name="option_text[]"]');
                if (optionInputs[selectedIndex - 1] && mcqCorrectAnswer) {
                    mcqCorrectAnswer.value = optionInputs[selectedIndex - 1].value || `Choice ${selectedIndex}`;
                }
            }
        });
        
        // Update the correct answer when changing the text of the options
        mcqWrap.addEventListener('input', function(e) {
            if (e.target.name === 'option_text[]') {
                const checkedRadio = mcqWrap.querySelector('input[name="correct_index"]:checked');
                if (checkedRadio && mcqCorrectAnswer) {
                    const selectedIndex = parseInt(checkedRadio.value);
                    const optionInputs = mcqWrap.querySelectorAll('input[name="option_text[]"]');
                    if (optionInputs[selectedIndex - 1]) {
                        mcqCorrectAnswer.value = optionInputs[selectedIndex - 1].value || `Choice ${selectedIndex}`;
                    }
                }
            }
        });
    }

    // Hide/show noQuestions According to the Count of elements
    const container = document.getElementById('questionsContainer');
    const noQ = document.getElementById('noQuestions');
    function refreshNoQ(){ if (!container || !noQ) return; noQ.classList.toggle('hidden', container.children.length > 0); }
    refreshNoQ();
</script>
