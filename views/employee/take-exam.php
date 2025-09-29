<?php /* The content only; General planning is included across Controller::render */
$examId = (int)($exam['id'] ?? 0);
$durationMinutes = (int)($exam['duration_minutes'] ?? 0);
$remainingSeconds = isset($remainingSeconds) && is_int($remainingSeconds) ? $remainingSeconds : ($durationMinutes * 60);
?>

<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- The good assessment head -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-lg p-8 mb-8 text-white">
        <div class="flex items-center justify-between mb-6">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-3"><?= htmlspecialchars($exam['title'] ?? '') ?></h1>
                <p class="text-blue-100 text-lg"><?= htmlspecialchars($exam['description'] ?? '') ?></p>
                <div class="flex items-center gap-6 mt-4 text-sm">
                    <div class="flex items-center">
                        <i class="ri-question-line mr-2"></i>
                        <span><?= count($questions) ?> Question</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-star-line mr-2"></i>
                        <span><?= $exam['total_points'] ?? (count($questions) * 10) ?> a point</span>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-6 text-center">
                    <div class="text-sm font-medium mb-2">The remaining time</div>
                    <div id="timer" class="text-3xl font-bold">--:--</div>
                </div>
            </div>
        </div>
        
        <!-- Improved progress bar -->
        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
            <div class="flex justify-between text-sm mb-3">
                <span>Progress in the assessment</span>
                <span id="progress-text">0 from <?= count($questions) ?></span>
            </div>
            <div class="w-full bg-white/30 rounded-full h-3">
                <div id="progress-bar" class="bg-white h-3 rounded-full transition-all duration-500 shadow-sm" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-4 gap-8">
        <!-- List of side questions -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-8">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="ri-list-check mr-2 text-blue-600"></i>
                    Questions map
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
                        <span>The current question</span>
                    </div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                        <span>Answer</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-gray-200 rounded"></div>
                        <span>The answer has not been answered</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions area -->
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
                                            <h3 class="text-xl font-bold text-gray-900">Question <?= $index + 1 ?></h3>
                                            <p class="text-sm text-gray-500">from <?= count($questions) ?> Questions</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                            <i class="ri-star-line mr-1"></i>
                                            <?= $question['points'] ?? 10 ?> a point
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8">
                                    <p class="text-gray-800 text-lg leading-relaxed"><?= htmlspecialchars($question['question_text'] ?? '') ?></p>
                                </div>

                                <!-- Moviest answer options -->
                                <div class="space-y-3">
                                    <?php if ($question['question_type'] === 'multiple_choice'): ?>
                                        <?php 
                                        // Add virtual options if it does not exist
                                        $originalChoices = $question['options'] ?? [];
                                        $isFallback = empty($originalChoices);
                                        $options = $originalChoices;
                                        if ($isFallback) {
                                            $options = [
                                                ['option_text' => 'An attempt to trick you into giving personal information'],
                                                ['option_text' => 'A type of electronic games'],
                                                ['option_text' => 'Virus protection program'],
                                                ['option_text' => 'A way to accelerate the Internet']
                                            ];
                                        }
                                        ?>
                                        <?php foreach ($options as $optIndex => $option): ?>
                                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <?php if ($isFallback): ?>
                                                    <input type="radio" name="question_<?= $question['id'] ?>" value="<?= htmlspecialchars($option['option_text'] ?? '') ?>" class="mr-3">
                                                <?php else: ?>
                                                    <input type="radio" name="question_<?= $question['id'] ?>" value="<?= (int)($option['id'] ?? 0) ?>" data-option-id="<?= (int)($option['id'] ?? 0) ?>" class="mr-3">
                                                <?php endif; ?>
                                                <span class="text-gray-900"><?= htmlspecialchars($option['option_text'] ?? '') ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    
                                    <?php elseif ($question['question_type'] === 'true_false'): ?>
                                        <?php
                                        // Bring options true_false From the database
                                        $tfChoices = [];
                                        if (isset($optionsByQ[$question['id']])) {
                                            $tfChoices = $optionsByQ[$question['id']];
                                        }
                                        ?>
                                        <?php if (!empty($tfChoices)): ?>
                                            <?php foreach ($tfChoices as $option): ?>
                                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                    <input type="radio" name="question_<?= $question['id'] ?>" value="<?= (int)$option['id'] ?>" data-option-id="<?= (int)$option['id'] ?>" class="mr-3">
                                                    <span class="text-gray-900"><?= htmlspecialchars($option['option_text']) ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <!-- Fallback For old questions - -->
                                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="radio" name="question_<?= $question['id'] ?>" value="TRUE" class="mr-3">
                                                <span class="text-gray-900">Right</span>
                                            </label>
                                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                <input type="radio" name="question_<?= $question['id'] ?>" value="FALSE" class="mr-3">
                                                <span class="text-gray-900">mistake</span>
                                            </label>
                                        <?php endif; ?>
                                    
                                    <?php elseif ($question['question_type'] === 'text'): ?>
                                        <textarea name="question_<?= $question['id'] ?>" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" rows="5" placeholder="Write your answer here in detail ... "></textarea>
                                    
                                    <?php else: ?>
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                            <p class="text-yellow-600">Non -supported question type: <?= htmlspecialchars($question['question_type'] ?? '') ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Enhanced control buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
                <div class="flex justify-between items-center">
                    <button id="prev-btn" class="flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <i class="ri-arrow-right-line mr-2"></i>
                        The previous question
                    </button>
                    
                    <div class="flex items-center gap-3">
                        <button id="save-draft-btn" class="flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl font-medium transition-all">
                            <i class="ri-save-line mr-2"></i>
                            Save a draft
                        </button>
                        <button id="next-btn" class="flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all">
                            The following question
                            <i class="ri-arrow-left-line mr-2"></i>
                        </button>
                        <button id="submit-btn" class="flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl font-medium transition-all hidden">
                            <i class="ri-send-plane-line mr-2"></i>
                            Finish the assessment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transmission confirmation window -->
<div id="submit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirm sending the assessment</h3>
        <p class="text-gray-600 mb-6">Are you sure to send the assessment? You will not be able to adjust your answers after sending.</p>
        <div class="flex justify-end space-x-3 space-x-reverse">
            <button id="cancel-submit" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">cancellation</button>
            <button id="confirm-submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Confirmation of the transmission</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questions = document.querySelectorAll('.question-container');
    const totalQuestions = questions.length;
    let currentQuestion = 1;
    
    // Assessment data
    const examData = <?= json_encode([
        'id' => (int)$examId,
        'duration' => (int)$durationMinutes,
        'title' => (string)($exam['title'] ?? ''),
    ], JSON_UNESCAPED_UNICODE) ?>;
    
    // Temporary
    let timeLeft = <?= (int)$remainingSeconds ?>; // Remaining
    const timerElement = document.getElementById('timer');
    
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            alert('The time specified for the assessment is over!');
            submitExam();
            return;
        }
        
        timeLeft--;
    }
    
    // Starting temporary
    // The remaining time is calculated from the server and was previously passed
    
    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();
    
    // Control items
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const saveDraftBtn = document.getElementById('save-draft-btn');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    // Update the progress bar
    function updateProgress() {
        const answeredQuestions = getAnsweredQuestionsCount();
        const percentage = (answeredQuestions / totalQuestions) * 100;
        if (progressBar) progressBar.style.width = percentage + '%';
        if (progressText) progressText.textContent = `${answeredQuestions} from ${totalQuestions}`;
    }
    
    // The Count of questions is answered
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
    
    // View the question
    function showQuestion(questionNum) {
        questions.forEach((q, index) => {
            q.classList.toggle('hidden', index + 1 !== questionNum);
        });
        
        currentQuestion = questionNum;
        
        // Mobility buttons update
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
    
    // Make showQuestion Available globally
    window.showQuestion = showQuestion;
    
    // Mobility between questions
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
    
    // Save the answer
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
                console.error('Save the answer failure. Status:', response.status, 'Body:', text);
                alert('Error in sending the answer: ' + response.status + '\n' + text);
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
                    alert('A mistake in analyzing the response from the server');
                    throw parseError;
                }
            } else {
                console.error('Non -response response JSON:', text);
                alert('An incorrect response from the server: \n' + text);
                throw new Error('Invalid JSON');
            }
        }).then(data => {
            if (data && data.success) {
                console.log('Answer saved successfully');
                // Immediate evaluation offer
                showImmediateFeedback(questionId, data);
            } else {
                console.error('Error save the answer:', data ? data.message : 'Unknown error');
                alert('Error save the answer: ' + (data ? data.message : 'Unknown error'));
            }
        }).catch(error => {
            console.error('Error while save the answer:', error);
            console.error('Error stack:', error.stack);
            alert('An error occurred during the transmission: ' + error.message);
        });
    }
    
    // View immediate assessment of the answer
    function showImmediateFeedback(questionId, feedbackData) {
        const questionElement = document.querySelector(`[data-question-id="${questionId}"]`);
        if (!questionElement) return;
        
        // Remove any previous evaluation
        const existingFeedback = questionElement.querySelector('.answer-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        // Create an assessment component
        const feedbackElement = document.createElement('div');
        feedbackElement.className = 'answer-feedback mt-4 p-4 rounded-lg border-2 transition-all duration-300';
        
        if (feedbackData.is_correct) {
            feedbackElement.classList.add('bg-green-50', 'border-green-200', 'text-green-800');
            feedbackElement.innerHTML = `
                <div class="flex items-center gap-2 mb-2">
                    <i class="ri-check-circle-fill text-green-600 text-xl"></i>
                    <span class="font-bold">A correct answer!</span>
                </div>
                <div class="text-sm">
                    <p><strong>Your answer:</strong> ${feedbackData.user_answer}</p>
                    <p><strong>Recorded points:</strong> ${feedbackData.points_earned} from ${feedbackData.max_points}</p>
                </div>
            `;
        } else {
            feedbackElement.classList.add('bg-red-50', 'border-red-200', 'text-red-800');
            feedbackElement.innerHTML = `
                <div class="flex items-center gap-2 mb-2">
                    <i class="ri-close-circle-fill text-red-600 text-xl"></i>
                    <span class="font-bold">The wrong answer</span>
                </div>
                <div class="text-sm">
                    <p><strong>Your answer:</strong> ${feedbackData.user_answer}</p>
                    <p><strong>the right answer:</strong> ${feedbackData.correct_answer}</p>
                    <p><strong>Recorded points:</strong> ${feedbackData.points_earned} from ${feedbackData.max_points}</p>
                </div>
            `;
        }
        
        // Add the evaluation to the question
        questionElement.appendChild(feedbackElement);
        
        // Update the question of the question on the map
        updateQuestionNavigationWithFeedback(questionId, feedbackData.is_correct);
        
        // Optical effect
        feedbackElement.style.opacity = '0';
        feedbackElement.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            feedbackElement.style.opacity = '1';
            feedbackElement.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // Update the question map with evaluation
    function updateQuestionNavigationWithFeedback(questionId, isCorrect) {
        const questionIndex = Array.from(questions).findIndex(q => q.dataset.questionId === questionId);
        if (questionIndex === -1) return;
        
        const navButtons = document.querySelectorAll('.question-nav-btn');
        const navBtn = navButtons[questionIndex];
        if (!navBtn) return;
        
        // Remove the previous colors
        navBtn.classList.remove('border-gray-200', 'border-blue-500', 'border-green-500', 'bg-blue-600', 'bg-green-500', 'text-white', 'text-gray-700');
        
        if (questionIndex + 1 === currentQuestion) {
            // The current question
            if (isCorrect) {
                navBtn.classList.add('border-green-500', 'bg-green-500', 'text-white');
            } else {
                navBtn.classList.add('border-red-500', 'bg-red-500', 'text-white');
            }
        } else {
            // The last question
            if (isCorrect) {
                navBtn.classList.add('border-green-500', 'bg-green-500', 'text-white');
            } else {
                navBtn.classList.add('border-red-500', 'bg-red-500', 'text-white');
            }
        }
        
        // Add an icon to the case
        const existingIcon = navBtn.querySelector('.status-icon');
        if (existingIcon) existingIcon.remove();
        
        const icon = document.createElement('i');
        icon.className = `status-icon ${isCorrect ? 'ri-check-line' : 'ri-close-line'} text-xs mr-1`;
        navBtn.appendChild(icon);
    }
    
    // Monitor the change of answers
    questions.forEach(question => {
        const questionId = question.dataset.questionId;
        const inputs = question.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                let payload = null;
                if (input.type === 'radio' && input.checked) {
                    const optId = input.dataset.optionId;
                    if (optId && /^\d+$/.assessment(String(optId))) {
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
    
    // Update the Questions Map
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
            
            // Update the state of the button
            navBtn.dataset.answered = hasAnswer ? 'true' : 'false';
            
            // Color update
            navBtn.classList.remove('border-gray-200', 'border-blue-500', 'border-green-500', 'bg-blue-600', 'bg-green-500', 'text-white');
            
            if (index + 1 === currentQuestion) {
                // The current question
                navBtn.classList.add('border-blue-500', 'bg-blue-600', 'text-white');
            } else if (hasAnswer) {
                // Answer
                navBtn.classList.add('border-green-500', 'bg-green-500', 'text-white');
            } else {
                // The answer has not been answered
                navBtn.classList.add('border-gray-200');
            }
        });
    }
    
    // Add the mobility function to the questions map buttons
    document.querySelectorAll('.question-nav-btn').forEach((btn, index) => {
        btn.addEventListener('click', () => {
            showQuestion(index + 1);
        });
    });
    
    // Save a draft
    saveDraftBtn.addEventListener('click', () => {
        // Save all the current answers
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
        
        // Save the current question
        localStorage.setItem(`exam_${examData.id}_current_question`, currentQuestion);
        
        alert('The draft was successfully saved!');
    });
    
    // Send the assessment
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
                console.error('Send the assessment failure. Status:', r.status, 'Body:', text);
                alert('Error in sending the assessment: ' + r.status + '\n' + text);
                throw new Error('HTTP '+r.status);
            }
            
            const text = await r.text();
            
            if (ct.includes('application/json')) {
                try {
                    const data = JSON.parse(text);
                    return data;
                } catch (parseError) {
                    console.error('Submit JSON parse error:', parseError);
                    alert('An error in analyzing the response of the assessment');
                    throw parseError;
                }
            } else {
                console.error('Non -response response JSON To send the assessment:', text);
                alert('An incorrect response from the server to send the assessment: \n' + text);
                throw new Error('Invalid JSON');
            }
        })
        .then(res => {
            if (res && res.success) {
                localStorage.removeItem(`exam_${examData.id}_current_question`);
                showResults(res);
            } else {
                console.error('Error in sending the assessment:', res ? res.message : 'Unknown error');
                alert(res && res.message ? res.message : 'The assessment could not be sent');
            }
        })
        .catch(err => {
            console.error('Error while sending the assessment:', err);
            alert('An error occurred during the transmission: ' + err.message);
        });
    }
    
    // View the results in detail and the correct/wrong answers
    function showResults(res) {
        // Remove the page to leave the page after sending the assessment
        window.removeEventListener('beforeunload', beforeUnloadHandler);
        
        // Hide control buttons
        if (prevBtn) prevBtn.disabled = true;
        if (nextBtn) nextBtn.disabled = true;
        if (submitBtn) submitBtn.disabled = true;
        if (saveDraftBtn) saveDraftBtn.disabled = true;
        
        // Disable all the input fields
        document.querySelectorAll('input, textarea, button.question-nav-btn').forEach(el => { el.disabled = true; });
        
        // Distinguish each question, show the correct answer, and the user's answer
        const detailsById = {};
        (res.details || []).forEach(d => { detailsById[String(d.question_id)] = d; });
        document.querySelectorAll('.question-container').forEach(container => {
            const qid = container.dataset.questionId;
            const d = detailsById[qid];
            if (!d) return;
            
            // Frame coloring by health
            container.classList.add('rounded-lg', 'p-2');
            container.style.borderWidth = '2px';
            container.style.borderStyle = 'solid';
            container.style.borderColor = d.is_correct ? '#16a34a' : '#dc2626';
            
            // Extract the text of the user's answer
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
            
            // The correct answer to the correct answer
            let correctText = '';
            if (d.correct_option_texts && d.correct_option_texts.length > 0) {
                correctText = d.correct_option_texts.join(', ');
            } else if (d.correct_text_answer) {
                correctText = String(d.correct_text_answer);
            }
            
            // The result box for each question (if not added)
            let resultBox = container.querySelector('.question-result-box');
            if (!resultBox) {
                resultBox = document.createElement('div');
                resultBox.className = 'question-result-box mt-4 p-4 rounded-lg';
                resultBox.style.background = d.is_correct ? '#ecfdf5' : '#fef2f2';
                resultBox.style.border = '1px solid ' + (d.is_correct ? '#10b981' : '#ef4444');
                resultBox.innerHTML = `
                    <div class="text-sm ${d.is_correct ? 'text-emerald-700' : 'text-red-700'} font-medium mb-2">
                        ${d.is_correct ? 'A correct answer' : 'The wrong answer'}
                    </div>
                    <div class="text-sm text-gray-700"><strong>Your answer:</strong> <span>${userAnswerText ? userAnswerText : '—'}</span></div>
                    <div class="text-sm text-gray-700 mt-1"><strong>the right answer:</strong> <span>${correctText ? correctText : '—'}</span></div>
                    <div class="text-xs text-gray-500 mt-2">Points: ${d.points}</div>
                `;
                container.appendChild(resultBox);
            }
        });
        
        // Show the summary of the top of the page
        const summary = document.createElement('div');
        summary.className = 'rounded-xl border-2 p-5 mb-6';
        summary.style.borderColor = res.passed ? '#10b981' : '#ef4444';
        summary.style.background = res.passed ? '#ecfdf5' : '#fef2f2';
        summary.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="text-lg font-bold ${res.passed ? 'text-emerald-700' : 'text-red-700'}">
                    ${res.passed ? 'Congratulations! I passed the assessment' : 'Unfortunately, the assessment did not pass'}
                </div>
                <div class="text-xl font-extrabold text-gray-900">${res.score}%</div>
            </div>
            <div class="text-sm text-gray-700 mt-2">
                The correct questions: ${res.correct_answers} from ${res.total_questions}
            </div>
        `;
        const container = document.querySelector('.max-w-5xl .bg-gradient-to-r');
        if (container && container.parentElement) {
            container.parentElement.insertBefore(summary, container.nextSibling);
            
            // Add the return button to the list of assessments
            const backButton = document.createElement('div');
            backButton.className = 'text-center mt-6';
            backButton.innerHTML = `
                <a href="${baseUrl}/exams" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all">
                    <i class="ri-arrow-right-line mr-2"></i>
                    Back to the assessment list
                </a>
            `;
            container.parentElement.insertBefore(backButton, summary.nextSibling);
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Automatic redirect after 3 seconds
            setTimeout(() => {
                window.location.href = `${baseUrl}/exams`;
            }, 300);
        }
    }
    
    // Recall the reserved answers
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
    
    // Recover the subject of the preserved question
    const savedQuestion = localStorage.getItem(`exam_${examData.id}_current_question`);
    if (savedQuestion && parseInt(savedQuestion) > 0 && parseInt(savedQuestion) <= totalQuestions) {
        showQuestion(parseInt(savedQuestion));
    } else {
        showQuestion(1);
        updateQuestionNavigation();
    }
    
    // Preventing the page to be closed without memorizing
    window.addEventListener('beforeunload', beforeUnloadHandler = (e) => {
        // Save the current position before leaving
        localStorage.setItem(`exam_${examData.id}_current_question`, currentQuestion);
        e.preventDefault();
        e.returnValue = 'You have a progress assessment. Are you sure to leave the page?';
    });
    
    // The primary path is calculated from the server as in public/index.php
    // It guarantees compatibility, whether the application is Move down a sub -folder or on the root
    const baseUrl = <?= json_encode((function(){
        $bp = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($bp === '/' || $bp === '\\') { $bp = ''; }
        return $bp;
    })(), JSON_UNESCAPED_UNICODE) ?>;
});
</script>
