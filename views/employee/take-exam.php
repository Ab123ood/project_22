<?php /* المحتوى فقط؛ التخطيط العام يُدرج عبر Controller::render */
$examId = (int)($exam['id'] ?? 0);
$durationMinutes = (int)($exam['duration_minutes'] ?? 0);
$remainingSeconds = isset($remainingSeconds) && is_int($remainingSeconds) ? $remainingSeconds : ($durationMinutes * 60);
?>

<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- رأس الاختبار المحسن -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-lg p-8 mb-8 text-white">
        <div class="flex items-center justify-between mb-6">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-3"><?= htmlspecialchars($exam['title'] ?? '') ?></h1>
                <p class="text-blue-100 text-lg"><?= htmlspecialchars($exam['description'] ?? '') ?></p>
                <div class="flex items-center gap-6 mt-4 text-sm">
                    <div class="flex items-center">
                        <i class="ri-question-line ml-2"></i>
                        <span><?= count($questions) ?> سؤال</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-star-line ml-2"></i>
                        <span><?= $exam['total_points'] ?? (count($questions) * 10) ?> نقطة</span>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-6 text-center">
                    <div class="text-sm font-medium mb-2">الوقت المتبقي</div>
                    <div id="timer" class="text-3xl font-bold">--:--</div>
                </div>
            </div>
        </div>
        
        <!-- شريط التقدم المحسن -->
        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
            <div class="flex justify-between text-sm mb-3">
                <span>التقدم في الاختبار</span>
                <span id="progress-text">0 من <?= count($questions) ?></span>
            </div>
            <div class="w-full bg-white/30 rounded-full h-3">
                <div id="progress-bar" class="bg-white h-3 rounded-full transition-all duration-500 shadow-sm" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-4 gap-8">
        <!-- قائمة الأسئلة الجانبية -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-8">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="ri-list-check ml-2 text-blue-600"></i>
                    خريطة الأسئلة
                </h3>
                <div class="grid grid-cols-5 lg:grid-cols-4 gap-2">
                    <?php for ($i = 1; $i <= count($questions); $i++): ?>
                        <button class="question-nav-btn w-10 h-10 rounded-lg border-2 border-gray-200 text-sm font-medium transition-all hover:border-blue-400 hover:bg-blue-50 cursor-pointer transition-all duration-200">
                            <?= $i ?>
                        </button>
                    <?php endfor; ?>
                </div>
                <div class="mt-4 text-xs text-gray-500">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-3 h-3 bg-blue-600 rounded"></div>
                        <span>السؤال الحالي</span>
                    </div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                        <span>تم الإجابة</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-gray-200 rounded"></div>
                        <span>لم تتم الإجابة</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- منطقة الأسئلة -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <?php if (!empty($questions)): ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="question-container <?= $index === 0 ? '' : 'hidden' ?>" data-question="<?= $index + 1 ?>" data-question-id="<?= $question['id'] ?>">
                            <div class="mb-8">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-bold"><?= $index + 1 ?></span>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">السؤال <?= $index + 1 ?></h3>
                                            <p class="text-sm text-gray-500">من <?= count($questions) ?> أسئلة</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                            <i class="ri-star-line ml-1"></i>
                                            <?= $question['points'] ?? 10 ?> نقطة
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8">
                                    <p class="text-gray-800 text-lg leading-relaxed"><?= htmlspecialchars($question['question_text'] ?? '') ?></p>
                                </div>

                                <!-- خيارات الإجابة المحسنة -->
                                <div class="space-y-3">
                                    <?php if ($question['question_type'] === 'multiple_choice'): ?>
                                        <?php 
                                        // إضافة خيارات افتراضية إذا لم توجد
                                        $originalOptions = $question['options'] ?? [];
                                        $isFallback = empty($originalOptions);
                                        $options = $originalOptions;
                                        if ($isFallback) {
                                            $options = [
                                                ['option_text' => 'محاولة خداع للحصول على معلومات شخصية'],
                                                ['option_text' => 'نوع من الألعاب الإلكترونية'],
                                                ['option_text' => 'برنامج حماية من الفيروسات'],
                                                ['option_text' => 'طريقة لتسريع الإنترنت']
                                            ];
                                        }
                                        ?>
                                        <?php foreach ($options as $optIndex => $option): ?>
                                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <?php if ($isFallback): ?>
                                                    <input type="radio" name="question_<?= $question['id'] ?>" value="<?= htmlspecialchars($option['option_text'] ?? '') ?>" class="ml-3">
                                                <?php else: ?>
                                                    <input type="radio" name="question_<?= $question['id'] ?>" value="<?= (int)($option['id'] ?? 0) ?>" data-option-id="<?= (int)($option['id'] ?? 0) ?>" class="ml-3">
                                                <?php endif; ?>
                                                <span class="text-gray-900"><?= htmlspecialchars($option['option_text'] ?? '') ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    
                                    <?php elseif ($question['question_type'] === 'true_false'): ?>
                                        <?php
                                        // جلب خيارات true_false من قاعدة البيانات
                                        $tfOptions = [];
                                        if (isset($optionsByQ[$question['id']])) {
                                            $tfOptions = $optionsByQ[$question['id']];
                                        }
                                        ?>
                                        <?php if (!empty($tfOptions)): ?>
                                            <?php foreach ($tfOptions as $option): ?>
                                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                    <input type="radio" name="question_<?= $question['id'] ?>" value="<?= (int)$option['id'] ?>" data-option-id="<?= (int)$option['id'] ?>" class="ml-3">
                                                    <span class="text-gray-900"><?= htmlspecialchars($option['option_text']) ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <!-- Fallback للأسئلة القديمة -->
                                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="radio" name="question_<?= $question['id'] ?>" value="TRUE" class="ml-3">
                                                <span class="text-gray-900">صحيح</span>
                                            </label>
                                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="radio" name="question_<?= $question['id'] ?>" value="FALSE" class="ml-3">
                                                <span class="text-gray-900">خطأ</span>
                                            </label>
                                        <?php endif; ?>
                                    
                                    <?php elseif ($question['question_type'] === 'text'): ?>
                                        <textarea name="question_<?= $question['id'] ?>" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" rows="5" placeholder="اكتب إجابتك هنا بالتفصيل..."></textarea>
                                    
                                    <?php else: ?>
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                            <p class="text-yellow-600">نوع سؤال غير مدعوم: <?= htmlspecialchars($question['question_type'] ?? '') ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- أزرار التحكم المحسنة -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
                <div class="flex justify-between items-center">
                    <button id="prev-btn" class="flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i class="ri-arrow-right-line ml-2"></i>
                        السؤال السابق
                    </button>
                    
                    <div class="flex items-center gap-3">
                        <button id="save-draft-btn" class="flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl font-medium transition-all">
                            <i class="ri-save-line ml-2"></i>
                            حفظ مسودة
                        </button>
                        <button id="next-btn" class="flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all">
                            السؤال التالي
                            <i class="ri-arrow-left-line mr-2"></i>
                        </button>
                        <button id="submit-btn" class="flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl font-medium transition-all hidden">
                            <i class="ri-send-plane-line ml-2"></i>
                            إنهاء الاختبار
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة تأكيد الإرسال -->
<div id="submit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">تأكيد إرسال الاختبار</h3>
        <p class="text-gray-600 mb-6">هل أنت متأكد من إرسال الاختبار؟ لن تتمكن من تعديل إجاباتك بعد الإرسال.</p>
        <div class="flex justify-end space-x-3 space-x-reverse">
            <button id="cancel-submit" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">إلغاء</button>
            <button id="confirm-submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">تأكيد الإرسال</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questions = document.querySelectorAll('.question-container');
    const totalQuestions = questions.length;
    let currentQuestion = 1;
    
    // بيانات الاختبار
    const examData = <?= json_encode([
        'id' => (int)$examId,
        'duration' => (int)$durationMinutes,
        'title' => (string)($exam['title'] ?? ''),
    ], JSON_UNESCAPED_UNICODE) ?>;
    
    // المؤقت
    let timeLeft = <?= (int)$remainingSeconds ?>; // ثواني متبقية
    const timerElement = document.getElementById('timer');
    
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            alert('انتهى الوقت المحدد للاختبار!');
            submitExam();
            return;
        }
        
        timeLeft--;
    }
    
    // بدء المؤقت
    // الوقت المتبقي محسوب من الخادم وتم تمريره مسبقاً
    
    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
    
    // عناصر التحكم
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const saveDraftBtn = document.getElementById('save-draft-btn');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    // تحديث شريط التقدم
    function updateProgress() {
        const answeredQuestions = getAnsweredQuestionsCount();
        const percentage = (answeredQuestions / totalQuestions) * 100;
        if (progressBar) progressBar.style.width = percentage + '%';
        if (progressText) progressText.textContent = `${answeredQuestions} من ${totalQuestions}`;
    }
    
    // عدد الأسئلة المجابة
    function getAnsweredQuestionsCount() {
        let count = 0;
        questions.forEach(question => {
            const inputs = question.querySelectorAll('input[type="radio"]:checked, textarea');
            const hasAnswer = Array.from(inputs).some(input => {
                if (input.type === 'radio') return input.checked;
                if (input.tagName === 'TEXTAREA') return input.value.trim() !== '';
                return false;
            });
            if (hasAnswer) count++;
        });
        return count;
    }
    
    // عرض السؤال
    function showQuestion(questionNum) {
        questions.forEach((q, index) => {
            q.classList.toggle('hidden', index + 1 !== questionNum);
        });
        
        currentQuestion = questionNum;
        
        // تحديث أزرار التنقل
        if (prevBtn) {
            prevBtn.disabled = currentQuestion === 1;
        }
        if (nextBtn) {
            nextBtn.classList.toggle('hidden', currentQuestion === totalQuestions);
        }
        if (submitBtn) {
            submitBtn.classList.toggle('hidden', currentQuestion !== totalQuestions);
        }
        
        updateProgress();
        updateQuestionNavigation();
    }
    
    // جعل showQuestion متاحة عالمياً
    window.showQuestion = showQuestion;
    
    // التنقل بين الأسئلة
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentQuestion > 1) {
                showQuestion(currentQuestion - 1);
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentQuestion < totalQuestions) {
                showQuestion(currentQuestion + 1);
            }
        });
    }
    
    // حفظ الإجابة
    function saveAnswer(questionId, payload) {
        const formData = new FormData();
        formData.append('exam_id', examData.id);
        formData.append('question_id', questionId);
        if (payload && payload.optionId) {
            formData.append('option_id', String(payload.optionId));
        } else if (payload && payload.answerText !== undefined) {
            formData.append('answer_text', String(payload.answerText));
        }
        
        const url = `${baseUrl}/exams/save-answer`;
        
        fetch(url, {
            method: 'POST',
            body: formData
        }).then(async (response) => {
            if (!response.ok) {
                const text = await response.text();
                console.error('حفظ الإجابة فشل. Status:', response.status, 'Body:', text);
                alert('خطأ في إرسال الإجابة: ' + response.status + '\n' + text);
                throw new Error('HTTP '+response.status);
            }
            
            const text = await response.text();
            
            if (response.headers.get('content-type')?.includes('application/json')) {
                try {
                    const data = JSON.parse(text);
                    return data;
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response text that failed to parse:', text);
                    alert('خطأ في تحليل الاستجابة من الخادم');
                    throw parseError;
                }
            } else {
                console.error('استجابة غير JSON:', text);
                alert('استجابة غير صحيحة من الخادم:\n' + text);
                throw new Error('Invalid JSON');
            }
        }).then(data => {
            if (data && data.success) {
                console.log('Answer saved successfully');
                // عرض التقييم الفوري
                showImmediateFeedback(questionId, data);
            } else {
                console.error('خطأ في حفظ الإجابة:', data ? data.message : 'Unknown error');
                alert('خطأ في حفظ الإجابة: ' + (data ? data.message : 'Unknown error'));
            }
        }).catch(error => {
            console.error('خطأ أثناء حفظ الإجابة:', error);
            console.error('Error stack:', error.stack);
            alert('حدث خطأ أثناء الإرسال: ' + error.message);
        });
    }
    
    // عرض التقييم الفوري للإجابة
    function showImmediateFeedback(questionId, feedbackData) {
        const questionElement = document.querySelector(`[data-question-id="${questionId}"]`);
        if (!questionElement) return;
        
        // إزالة أي تقييم سابق
        const existingFeedback = questionElement.querySelector('.answer-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        // إنشاء عنصر التقييم
        const feedbackElement = document.createElement('div');
        feedbackElement.className = 'answer-feedback mt-4 p-4 rounded-lg border-2 transition-all duration-300';
        
        if (feedbackData.is_correct) {
            feedbackElement.classList.add('bg-green-50', 'border-green-200', 'text-green-800');
            feedbackElement.innerHTML = `
                <div class="flex items-center gap-2 mb-2">
                    <i class="ri-check-circle-fill text-green-600 text-xl"></i>
                    <span class="font-bold">إجابة صحيحة!</span>
                </div>
                <div class="text-sm">
                    <p><strong>إجابتك:</strong> ${feedbackData.user_answer}</p>
                    <p><strong>النقاط المكتسبة:</strong> ${feedbackData.points_earned} من ${feedbackData.max_points}</p>
                </div>
            `;
        } else {
            feedbackElement.classList.add('bg-red-50', 'border-red-200', 'text-red-800');
            feedbackElement.innerHTML = `
                <div class="flex items-center gap-2 mb-2">
                    <i class="ri-close-circle-fill text-red-600 text-xl"></i>
                    <span class="font-bold">إجابة خاطئة</span>
                </div>
                <div class="text-sm">
                    <p><strong>إجابتك:</strong> ${feedbackData.user_answer}</p>
                    <p><strong>الإجابة الصحيحة:</strong> ${feedbackData.correct_answer}</p>
                    <p><strong>النقاط المكتسبة:</strong> ${feedbackData.points_earned} من ${feedbackData.max_points}</p>
                </div>
            `;
        }
        
        // إضافة التقييم إلى السؤال
        questionElement.appendChild(feedbackElement);
        
        // تحديث حالة السؤال في الخريطة
        updateQuestionNavigationWithFeedback(questionId, feedbackData.is_correct);
        
        // تأثير بصري
        feedbackElement.style.opacity = '0';
        feedbackElement.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            feedbackElement.style.opacity = '1';
            feedbackElement.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // تحديث خريطة الأسئلة مع التقييم
    function updateQuestionNavigationWithFeedback(questionId, isCorrect) {
        const questionIndex = Array.from(questions).findIndex(q => q.dataset.questionId === questionId);
        if (questionIndex === -1) return;
        
        const navButtons = document.querySelectorAll('.question-nav-btn');
        const navBtn = navButtons[questionIndex];
        if (!navBtn) return;
        
        // إزالة الألوان السابقة
        navBtn.classList.remove('border-gray-200', 'border-blue-500', 'border-green-500', 'bg-blue-600', 'bg-green-500', 'text-white', 'text-gray-700');
        
        if (questionIndex + 1 === currentQuestion) {
            // السؤال الحالي
            if (isCorrect) {
                navBtn.classList.add('border-green-500', 'bg-green-500', 'text-white');
            } else {
                navBtn.classList.add('border-red-500', 'bg-red-500', 'text-white');
            }
        } else {
            // سؤال آخر
            if (isCorrect) {
                navBtn.classList.add('border-green-500', 'bg-green-500', 'text-white');
            } else {
                navBtn.classList.add('border-red-500', 'bg-red-500', 'text-white');
            }
        }
        
        // إضافة أيقونة للحالة
        const existingIcon = navBtn.querySelector('.status-icon');
        if (existingIcon) existingIcon.remove();
        
        const icon = document.createElement('i');
        icon.className = `status-icon ${isCorrect ? 'ri-check-line' : 'ri-close-line'} text-xs ml-1`;
        navBtn.appendChild(icon);
    }
    
    // مراقبة تغيير الإجابات
    questions.forEach(question => {
        const questionId = question.dataset.questionId;
        const inputs = question.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                let payload = null;
                if (input.type === 'radio' && input.checked) {
                    const optId = input.dataset.optionId;
                    if (optId && /^\d+$/.test(String(optId))) {
                        payload = { optionId: optId };
                    } else {
                        payload = { answerText: input.value };
                    }
                } else if (input.tagName === 'TEXTAREA') {
                    payload = { answerText: input.value };
                }
                if (payload) {
                    saveAnswer(questionId, payload);
                }
                updateProgress();
                updateQuestionNavigation();
            });
        });
    });
    
    // تحديث خريطة الأسئلة
    function updateQuestionNavigation() {
        const navButtons = document.querySelectorAll('.question-nav-btn');
        
        questions.forEach((question, index) => {
            const navBtn = navButtons[index];
            if (!navBtn) return;
            
            const inputs = question.querySelectorAll('input[type="radio"]:checked, textarea');
            const hasAnswer = Array.from(inputs).some(input => {
                if (input.type === 'radio') return input.checked;
                if (input.tagName === 'TEXTAREA') return input.value.trim() !== '';
                return false;
            });
            
            // تحديث حالة الزر
            navBtn.dataset.answered = hasAnswer ? 'true' : 'false';
            
            // تحديث الألوان
            navBtn.classList.remove('border-gray-200', 'border-blue-500', 'border-green-500', 'bg-blue-600', 'bg-green-500', 'text-white');
            
            if (index + 1 === currentQuestion) {
                // السؤال الحالي
                navBtn.classList.add('border-blue-500', 'bg-blue-600', 'text-white');
            } else if (hasAnswer) {
                // تم الإجابة عليه
                navBtn.classList.add('border-green-500', 'bg-green-500', 'text-white');
            } else {
                // لم تتم الإجابة
                navBtn.classList.add('border-gray-200');
            }
        });
    }
    
    // إضافة وظيفة التنقل لأزرار خريطة الأسئلة
    document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
        btn.addEventListener('click', () => {
            showQuestion(index + 1);
        });
    });
    
    // حفظ مسودة
    saveDraftBtn.addEventListener('click', () => {
        // حفظ جميع الإجابات الحالية
        questions.forEach(question => {
            const questionId = question.dataset.questionId;
            let answer = '';
            
            const radioInput = question.querySelector('input[type="radio"]:checked');
            const textInput = question.querySelector('textarea');
            
            if (radioInput) {
                answer = radioInput.value;
            } else if (textInput && textInput.value.trim()) {
                answer = textInput.value;
            }
            
            if (answer) {
                saveAnswer(questionId, answer);
            }
        });
        
        // حفظ موضع السؤال الحالي
        localStorage.setItem(`exam_${examData.id}_current_question`, currentQuestion);
        
        alert('تم حفظ المسودة بنجاح!');
    });
    
    // إرسال الاختبار
    const submitModal = document.getElementById('submit-modal');
    const cancelSubmit = document.getElementById('cancel-submit');
    const confirmSubmit = document.getElementById('confirm-submit');
    
    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            if (submitModal) {
                submitModal.classList.remove('hidden');
                submitModal.classList.add('flex');
            }
        });
    }
    
    if (cancelSubmit) {
        cancelSubmit.addEventListener('click', () => {
            if (submitModal) {
                submitModal.classList.add('hidden');
                submitModal.classList.remove('flex');
            }
        });
    }
    
    if (confirmSubmit) {
        confirmSubmit.addEventListener('click', () => {
            submitExam();
        });
    }
    
    function submitExam() {
        clearInterval(timerInterval);
        
        const formData = new FormData();
        formData.append('exam_id', examData.id);
        fetch(`${baseUrl}/exams/submit`, {
            method: 'POST',
            body: formData
        })
        .then(async (r) => {
            const ct = r.headers.get('content-type') || '';
            
            if (!r.ok) {
                const text = await r.text();
                console.error('إرسال الاختبار فشل. Status:', r.status, 'Body:', text);
                alert('خطأ في إرسال الاختبار: ' + r.status + '\n' + text);
                throw new Error('HTTP '+r.status);
            }
            
            const text = await r.text();
            
            if (ct.includes('application/json')) {
                try {
                    const data = JSON.parse(text);
                    return data;
                } catch (parseError) {
                    console.error('Submit JSON parse error:', parseError);
                    alert('خطأ في تحليل استجابة إرسال الاختبار');
                    throw parseError;
                }
            } else {
                console.error('استجابة غير JSON لإرسال الاختبار:', text);
                alert('استجابة غير صحيحة من الخادم لإرسال الاختبار:\n' + text);
                throw new Error('Invalid JSON');
            }
        })
        .then(res => {
            if (res && res.success) {
                localStorage.removeItem(`exam_${examData.id}_current_question`);
                showResults(res);
            } else {
                console.error('خطأ في إرسال الاختبار:', res ? res.message : 'Unknown error');
                alert(res && res.message ? res.message : 'تعذر إرسال الاختبار');
            }
        })
        .catch(err => {
            console.error('خطأ أثناء إرسال الاختبار:', err);
            alert('حدث خطأ أثناء الإرسال: ' + err.message);
        });
    }
    
    // عرض النتائج بالتفصيل ووسم الإجابات الصحيحة/الخاطئة
    function showResults(res) {
        // إزالة تحذير مغادرة الصفحة بعد إرسال الاختبار
        window.removeEventListener('beforeunload', beforeUnloadHandler);
        
        // إخفاء أزرار التحكم
        if (prevBtn) prevBtn.disabled = true;
        if (nextBtn) nextBtn.disabled = true;
        if (submitBtn) submitBtn.disabled = true;
        if (saveDraftBtn) saveDraftBtn.disabled = true;
        
        // تعطيل جميع حقول الإدخال
        document.querySelectorAll('input, textarea, button.question-nav-btn').forEach(el => { el.disabled = true; });
        
        // تمييز كل سؤال وإظهار الإجابة الصحيحة وإجابة المستخدم
        const detailsById = {};
        (res.details || []).forEach(d => { detailsById[String(d.question_id)] = d; });
        document.querySelectorAll('.question-container').forEach(container => {
            const qid = container.dataset.questionId;
            const d = detailsById[qid];
            if (!d) return;
            
            // تلوين الإطار بحسب الصحة
            container.classList.add('rounded-lg', 'p-2');
            container.style.borderWidth = '2px';
            container.style.borderStyle = 'solid';
            container.style.borderColor = d.is_correct ? '#16a34a' : '#dc2626';
            
            // استخراج نص إجابة المستخدم
            let userAnswerText = '';
            if (d.user_selected_option_id) {
                const selected = container.querySelector(`input[type="radio"][value="${d.user_selected_option_id}"]`);
                if (selected) {
                    const label = selected.closest('label');
                    const span = label ? label.querySelector('span') : null;
                    userAnswerText = span ? span.textContent.trim() : '';
                }
            } else if (d.user_text_answer) {
                userAnswerText = String(d.user_text_answer).trim();
            }
            
            // نص الإجابة الصحيحة
            let correctText = '';
            if (d.correct_option_texts && d.correct_option_texts.length > 0) {
                correctText = d.correct_option_texts.join('، ');
            } else if (d.correct_text_answer) {
                correctText = String(d.correct_text_answer);
            }
            
            // صندوق النتيجة لكل سؤال (إن لم يكن مضافاً)
            let resultBox = container.querySelector('.question-result-box');
            if (!resultBox) {
                resultBox = document.createElement('div');
                resultBox.className = 'question-result-box mt-4 p-4 rounded-lg';
                resultBox.style.background = d.is_correct ? '#ecfdf5' : '#fef2f2';
                resultBox.style.border = '1px solid ' + (d.is_correct ? '#10b981' : '#ef4444');
                resultBox.innerHTML = `
                    <div class="text-sm ${d.is_correct ? 'text-emerald-700' : 'text-red-700'} font-medium mb-2">
                        ${d.is_correct ? 'إجابة صحيحة' : 'إجابة خاطئة'}
                    </div>
                    <div class="text-sm text-gray-700"><strong>إجابتك:</strong> <span>${userAnswerText ? userAnswerText : '—'}</span></div>
                    <div class="text-sm text-gray-700 mt-1"><strong>الإجابة الصحيحة:</strong> <span>${correctText ? correctText : '—'}</span></div>
                    <div class="text-xs text-gray-500 mt-2">النقاط: ${d.points}</div>
                `;
                container.appendChild(resultBox);
            }
        });
        
        // إظهار ملخص أعلى الصفحة
        const summary = document.createElement('div');
        summary.className = 'rounded-xl border-2 p-5 mb-6';
        summary.style.borderColor = res.passed ? '#10b981' : '#ef4444';
        summary.style.background = res.passed ? '#ecfdf5' : '#fef2f2';
        summary.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="text-lg font-bold ${res.passed ? 'text-emerald-700' : 'text-red-700'}">
                    ${res.passed ? 'تهانينا! لقد اجتزت الاختبار' : 'للأسف لم تجتز الاختبار'}
                </div>
                <div class="text-xl font-extrabold text-gray-900">${res.score}%</div>
            </div>
            <div class="text-sm text-gray-700 mt-2">
                الأسئلة الصحيحة: ${res.correct_answers} من ${res.total_questions}
            </div>
        `;
        const container = document.querySelector('.max-w-5xl .bg-gradient-to-r');
        if (container && container.parentElement) {
            container.parentElement.insertBefore(summary, container.nextSibling);
            
            // إضافة زر العودة إلى قائمة الاختبارات
            const backButton = document.createElement('div');
            backButton.className = 'text-center mt-6';
            backButton.innerHTML = `
                <a href="${baseUrl}/exams" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all">
                    <i class="ri-arrow-right-line ml-2"></i>
                    العودة إلى قائمة الاختبارات
                </a>
            `;
            container.parentElement.insertBefore(backButton, summary.nextSibling);
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // إعادة توجيه تلقائي بعد 3 ثوان
            setTimeout(() => {
                window.location.href = `${baseUrl}/exams`;
            }, 300);
        }
    }
    
    // استرجاع الإجابات المحفوظة
    <?php if (!empty($savedAnswers)): ?>
        const savedAnswers = <?= json_encode($savedAnswers, JSON_UNESCAPED_UNICODE) ?>;
        Object.keys(savedAnswers).forEach(qid => {
            const data = savedAnswers[qid];
            const container = document.querySelector(`[data-question-id="${qid}"]`);
            if (!container) return;
            if (data.option_id) {
                const radio = container.querySelector(`input[type="radio"][value="${data.option_id}"]`);
                if (radio) radio.checked = true;
            } else if (data.answer_text) {
                const textarea = container.querySelector('textarea');
                if (textarea) textarea.value = data.answer_text;
            }
        });
        updateProgress();
        updateQuestionNavigation();
    <?php endif; ?>
    
    // استرجاع موضع السؤال المحفوظ
    const savedQuestion = localStorage.getItem(`exam_${examData.id}_current_question`);
    if (savedQuestion && parseInt(savedQuestion) > 0 && parseInt(savedQuestion) <= totalQuestions) {
        showQuestion(parseInt(savedQuestion));
    } else {
        showQuestion(1);
        updateQuestionNavigation();
    }
    
    // منع إغلاق الصفحة بدون حفظ
    window.addEventListener('beforeunload', beforeUnloadHandler = (e) => {
        // حفظ الموضع الحالي قبل المغادرة
        localStorage.setItem(`exam_${examData.id}_current_question`, currentQuestion);
        e.preventDefault();
        e.returnValue = 'لديك اختبار قيد التقدم. هل أنت متأكد من مغادرة الصفحة؟';
    });
    
    // المسار الأساسي محسوب من الخادم كما في public/index.php
    // يضمن التوافق سواء كان التطبيق تحت مجلد فرعي أو على الجذر
    const baseUrl = <?= json_encode((function(){
        $bp = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($bp === '/' || $bp === '\\') { $bp = ''; }
        return $bp;
    })(), JSON_UNESCAPED_UNICODE) ?>;
});
</script>
