/**
 * Educational Games - Game Types JavaScript
 * Interactive functionality for the 7 educational game types
 */

// Game data storage
var gameData = {
    questions: []
};

// Game type specific data
var gameTypeData = {
    mathQuestions: [],
    wordQuestions: [],
    memoryPairs: [],
    sortingLists: [],
    trueFalseQuestions: [],
    puzzleQuestions: [],
    vocabQuestions: []
};

/**
 * Load game type interface based on selected category
 */
function loadGameTypeInterface(categoryId, categoryName) {
    if (!categoryName) return;
    
    $.ajax({
        url: base_url + 'game_types/get_game_interface',
        type: 'POST',
        data: {
            game_type: categoryName,
            total_questions: $('#total_questions').val() || 5
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#questions-container').html(response.html);
                
                // Show custom word section for vocabulary if needed
                if (categoryName === 'Vocabulary Builder') {
                    $('#word-list').on('change', function() {
                        if ($(this).val() === 'custom') {
                            $('#custom-words-section').show();
                        } else {
                            $('#custom-words-section').hide();
                        }
                    });
                }
                
                $('#submit-btn').prop('disabled', false);
            }
        },
        error: function() {
            alert('Error loading game interface. Please try again.');
        }
    });
}

/**
 * Math Quiz Functions
 */
function generateMathQuestions(totalQuestions) {
    var operation = $('#math-operation').val();
    var difficulty = $('#math-difficulty').val();
    var range = difficulty.split('-');
    var minNum = parseInt(range[0]);
    var maxNum = parseInt(range[1]);
    
    gameTypeData.mathQuestions = [];
    var questionsHtml = '<div class="panel panel-info"><div class="panel-heading"><strong>Generated Math Questions</strong></div><div class="panel-body">';
    
    for (var i = 1; i <= totalQuestions; i++) {
        var question = generateSingleMathQuestion(operation, minNum, maxNum, i);
        gameTypeData.mathQuestions.push(question);
        
        questionsHtml += '<div class="question-item">';
        questionsHtml += '<h5>Question ' + i + '</h5>';
        questionsHtml += '<p><strong>' + question.question + '</strong></p>';
        questionsHtml += '<div class="row">';
        
        question.options.forEach(function(option, index) {
            questionsHtml += '<div class="col-md-3">';
            questionsHtml += '<label class="radio-inline">';
            questionsHtml += '<input type="radio" name="math_q' + i + '" value="' + index + '"> ' + option;
            questionsHtml += '</label>';
            questionsHtml += '</div>';
        });
        
        questionsHtml += '</div>';
        questionsHtml += '<small class="text-muted">Correct answer: ' + question.options[question.correct_answer] + '</small>';
        questionsHtml += '<hr>';
        questionsHtml += '</div>';
    }
    
    questionsHtml += '</div></div>';
    $('#math-questions-container').html(questionsHtml);
    
    // Update global game data
    gameData.questions = gameTypeData.mathQuestions;
    $('#game_data').val(JSON.stringify(gameData));
}

function generateSingleMathQuestion(operation, minNum, maxNum, questionId) {
    var num1 = Math.floor(Math.random() * (maxNum - minNum + 1)) + minNum;
    var num2 = Math.floor(Math.random() * (maxNum - minNum + 1)) + minNum;
    var question = '';
    var correctAnswer = 0;
    
    switch (operation) {
        case 'addition':
            question = num1 + ' + ' + num2 + ' = ?';
            correctAnswer = num1 + num2;
            break;
        case 'subtraction':
            if (num1 < num2) [num1, num2] = [num2, num1]; // Ensure positive result
            question = num1 + ' - ' + num2 + ' = ?';
            correctAnswer = num1 - num2;
            break;
        case 'multiplication':
            question = num1 + ' × ' + num2 + ' = ?';
            correctAnswer = num1 * num2;
            break;
        case 'division':
            correctAnswer = num1;
            num1 = num1 * num2; // Ensure whole number division
            question = num1 + ' ÷ ' + num2 + ' = ?';
            break;
        case 'mixed':
            var ops = ['addition', 'subtraction', 'multiplication'];
            var randomOp = ops[Math.floor(Math.random() * ops.length)];
            return generateSingleMathQuestion(randomOp, minNum, maxNum, questionId);
    }
    
    // Generate multiple choice options
    var options = [correctAnswer];
    while (options.length < 4) {
        var wrongAnswer = correctAnswer + Math.floor(Math.random() * 21) - 10; // ±10 range
        if (wrongAnswer !== correctAnswer && wrongAnswer > 0 && options.indexOf(wrongAnswer) === -1) {
            options.push(wrongAnswer);
        }
    }
    
    // Shuffle options
    options.sort(() => Math.random() - 0.5);
    var correctIndex = options.indexOf(correctAnswer);
    
    return {
        id: questionId,
        question: question,
        type: 'multiple_choice',
        options: options,
        correct_answer: correctIndex
    };
}

/**
 * Word Completion Functions
 */
function generateWordQuestions(totalQuestions) {
    var category = $('#word-category').val();
    var completionType = $('#completion-type').val();
    
    var wordLists = {
        animals: ['elephant', 'giraffe', 'penguin', 'butterfly', 'crocodile', 'dolphin', 'kangaroo', 'rhinoceros'],
        colors: ['purple', 'orange', 'yellow', 'crimson', 'turquoise', 'magenta', 'violet', 'emerald'],
        food: ['sandwich', 'chocolate', 'strawberry', 'spaghetti', 'hamburger', 'broccoli', 'pineapple', 'avocado'],
        countries: ['australia', 'brazil', 'canada', 'denmark', 'egypt', 'france', 'germany', 'india'],
        science: ['molecule', 'gravity', 'photosynthesis', 'electricity', 'magnetism', 'ecosystem', 'evolution', 'chromosome']
    };
    
    var selectedWords = wordLists[category] || wordLists.animals;
    gameTypeData.wordQuestions = [];
    
    var questionsHtml = '<div class="panel panel-success"><div class="panel-heading"><strong>Generated Word Questions</strong></div><div class="panel-body">';
    
    for (var i = 1; i <= totalQuestions; i++) {
        var word = selectedWords[Math.floor(Math.random() * selectedWords.length)];
        var question = generateSingleWordQuestion(word, completionType, i);
        gameTypeData.wordQuestions.push(question);
        
        questionsHtml += '<div class="question-item">';
        questionsHtml += '<h5>Question ' + i + '</h5>';
        questionsHtml += '<p><strong>' + question.question + '</strong></p>';
        
        if (question.type === 'multiple_choice') {
            questionsHtml += '<div class="row">';
            question.options.forEach(function(option, index) {
                questionsHtml += '<div class="col-md-3">';
                questionsHtml += '<label class="radio-inline">';
                questionsHtml += '<input type="radio" name="word_q' + i + '" value="' + index + '"> ' + option;
                questionsHtml += '</label>';
                questionsHtml += '</div>';
            });
            questionsHtml += '</div>';
        } else {
            questionsHtml += '<input type="text" class="form-control" placeholder="Type your answer here" style="max-width: 300px;">';
        }
        
        questionsHtml += '<small class="text-muted">Correct answer: ' + word + '</small>';
        questionsHtml += '<hr>';
        questionsHtml += '</div>';
    }
    
    questionsHtml += '</div></div>';
    $('#word-questions-container').html(questionsHtml);
    
    gameData.questions = gameTypeData.wordQuestions;
    $('#game_data').val(JSON.stringify(gameData));
}

function generateSingleWordQuestion(word, completionType, questionId) {
    var question = '';
    var correctAnswer = word;
    
    switch (completionType) {
        case 'missing-letters':
            var maskedWord = maskRandomLetters(word);
            question = 'Complete the word: ' + maskedWord;
            break;
        case 'scrambled':
            var scrambledWord = scrambleWord(word);
            question = 'Unscramble this word: ' + scrambledWord;
            break;
        case 'fill-blanks':
            question = 'Fill in the blank: This animal is called a ______';
            // You can customize this based on category
            break;
    }
    
    // Generate multiple choice options
    var options = [word];
    var similarWords = generateSimilarWords(word);
    options = options.concat(similarWords.slice(0, 3));
    options.sort(() => Math.random() - 0.5);
    
    return {
        id: questionId,
        question: question,
        type: 'multiple_choice',
        options: options,
        correct_answer: options.indexOf(word)
    };
}

function maskRandomLetters(word) {
    var masked = word.split('');
    var numToMask = Math.max(1, Math.floor(word.length / 3));
    
    for (var i = 0; i < numToMask; i++) {
        var randomIndex = Math.floor(Math.random() * word.length);
        masked[randomIndex] = '_';
    }
    
    return masked.join('');
}

function scrambleWord(word) {
    return word.split('').sort(() => Math.random() - 0.5).join('');
}

function generateSimilarWords(word) {
    // Simple similar word generation (you can expand this)
    var similar = [];
    var letters = 'abcdefghijklmnopqrstuvwxyz';
    
    for (var i = 0; i < 3; i++) {
        var similar_word = word.slice(0, -1) + letters[Math.floor(Math.random() * letters.length)];
        similar.push(similar_word);
    }
    
    return similar;
}

/**
 * Memory Match Functions
 */
function generateMemoryQuestions(totalQuestions) {
    var memoryType = $('#memory-type').val();
    var gridSize = $('#grid-size').val();
    
    gameTypeData.memoryPairs = [];
    
    var questionsHtml = '<div class="panel panel-warning"><div class="panel-heading"><strong>Memory Match Game Setup</strong></div><div class="panel-body">';
    questionsHtml += '<div class="alert alert-info">Memory match games will be presented as interactive card matching during gameplay.</div>';
    
    var pairs = generateMemoryPairs(memoryType, gridSize);
    gameTypeData.memoryPairs = pairs;
    
    questionsHtml += '<h5>Generated Pairs:</h5>';
    questionsHtml += '<div class="row">';
    
    pairs.forEach(function(pair, index) {
        questionsHtml += '<div class="col-md-4">';
        questionsHtml += '<div class="panel panel-default">';
        questionsHtml += '<div class="panel-body text-center">';
        questionsHtml += '<strong>Pair ' + (index + 1) + '</strong><br>';
        questionsHtml += pair.item1 + ' ↔ ' + pair.item2;
        questionsHtml += '</div>';
        questionsHtml += '</div>';
        questionsHtml += '</div>';
    });
    
    questionsHtml += '</div>';
    questionsHtml += '</div></div>';
    $('#memory-questions-container').html(questionsHtml);
    
    // Create game data structure for memory match
    gameData.questions = [{
        id: 1,
        question: 'Match the pairs by clicking on the cards',
        type: 'memory_match',
        pairs: pairs,
        grid_size: gridSize,
        correct_answer: pairs.length // Number of pairs to match
    }];
    
    $('#game_data').val(JSON.stringify(gameData));
}

function generateMemoryPairs(memoryType, gridSize) {
    var pairCount = parseInt(gridSize.split('x')[0]) * parseInt(gridSize.split('x')[1]) / 2;
    var pairs = [];
    
    var dataSets = {
        numbers: [
            {item1: '1', item2: 'One'}, {item1: '2', item2: 'Two'}, {item1: '3', item2: 'Three'},
            {item1: '4', item2: 'Four'}, {item1: '5', item2: 'Five'}, {item1: '6', item2: 'Six'},
            {item1: '7', item2: 'Seven'}, {item1: '8', item2: 'Eight'}
        ],
        equations: [
            {item1: '2+2', item2: '4'}, {item1: '3+3', item2: '6'}, {item1: '4+4', item2: '8'},
            {item1: '5+5', item2: '10'}, {item1: '6+6', item2: '12'}, {item1: '7+7', item2: '14'},
            {item1: '8+8', item2: '16'}, {item1: '9+9', item2: '18'}
        ],
        definitions: [
            {item1: 'Cat', item2: 'Animal that meows'}, {item1: 'Sun', item2: 'Star in our solar system'},
            {item1: 'Book', item2: 'Object you read'}, {item1: 'Water', item2: 'H2O liquid'},
            {item1: 'Tree', item2: 'Tall plant with trunk'}, {item1: 'Car', item2: 'Vehicle with wheels'}
        ]
    };
    
    var selectedSet = dataSets[memoryType] || dataSets.numbers;
    
    for (var i = 0; i < Math.min(pairCount, selectedSet.length); i++) {
        pairs.push(selectedSet[i]);
    }
    
    return pairs;
}

/**
 * List Sorting Functions
 */
function generateSortingQuestions(totalQuestions) {
    var category = $('#sorting-category').val();
    var listLength = parseInt($('#list-length').val());
    
    gameTypeData.sortingLists = [];
    
    var questionsHtml = '<div class="panel panel-primary"><div class="panel-heading"><strong>Generated Sorting Questions</strong></div><div class="panel-body">';
    
    for (var i = 1; i <= totalQuestions; i++) {
        var sortingQuestion = generateSingleSortingQuestion(category, listLength, i);
        gameTypeData.sortingLists.push(sortingQuestion);
        
        questionsHtml += '<div class="question-item">';
        questionsHtml += '<h5>Question ' + i + '</h5>';
        questionsHtml += '<p><strong>' + sortingQuestion.question + '</strong></p>';
        questionsHtml += '<div class="row">';
        questionsHtml += '<div class="col-md-6">';
        questionsHtml += '<h6>Items to sort:</h6>';
        questionsHtml += '<ul>';
        sortingQuestion.unsorted_items.forEach(function(item) {
            questionsHtml += '<li>' + item + '</li>';
        });
        questionsHtml += '</ul>';
        questionsHtml += '</div>';
        questionsHtml += '<div class="col-md-6">';
        questionsHtml += '<h6>Correct order:</h6>';
        questionsHtml += '<ol>';
        sortingQuestion.correct_order.forEach(function(item) {
            questionsHtml += '<li>' + item + '</li>';
        });
        questionsHtml += '</ol>';
        questionsHtml += '</div>';
        questionsHtml += '</div>';
        questionsHtml += '<hr>';
        questionsHtml += '</div>';
    }
    
    questionsHtml += '</div></div>';
    $('#sorting-questions-container').html(questionsHtml);
    
    gameData.questions = gameTypeData.sortingLists;
    $('#game_data').val(JSON.stringify(gameData));
}

function generateSingleSortingQuestion(category, listLength, questionId) {
    var items = [];
    var question = '';
    
    switch (category) {
        case 'alphabetical':
            items = ['Apple', 'Banana', 'Cherry', 'Date', 'Elderberry', 'Fig', 'Grape', 'Honeydew'];
            question = 'Sort these words in alphabetical order:';
            break;
        case 'numerical':
            items = [];
            for (var i = 0; i < listLength; i++) {
                items.push(Math.floor(Math.random() * 100) + 1);
            }
            question = 'Sort these numbers from smallest to largest:';
            break;
        case 'chronological':
            items = ['Morning', 'Afternoon', 'Evening', 'Night', 'Dawn', 'Dusk'];
            question = 'Sort these times in chronological order:';
            break;
        case 'size':
            items = ['Ant', 'Mouse', 'Cat', 'Dog', 'Horse', 'Elephant'];
            question = 'Sort these animals from smallest to largest:';
            break;
    }
    
    var selectedItems = items.slice(0, listLength);
    var correctOrder = [...selectedItems].sort();
    
    if (category === 'numerical') {
        correctOrder = selectedItems.sort((a, b) => a - b);
    }
    
    var unsortedItems = [...selectedItems].sort(() => Math.random() - 0.5);
    
    return {
        id: questionId,
        question: question,
        type: 'sorting',
        unsorted_items: unsortedItems,
        correct_order: correctOrder,
        correct_answer: correctOrder.join(',')
    };
}

/**
 * True/False Functions
 */
function generateTrueFalseQuestions(totalQuestions) {
    var subject = $('#tf-subject').val();
    var statementType = $('#statement-type').val();
    
    gameTypeData.trueFalseQuestions = [];
    
    var questionsHtml = '<div class="panel panel-info"><div class="panel-heading"><strong>Generated True/False Questions</strong></div><div class="panel-body">';
    
    for (var i = 1; i <= totalQuestions; i++) {
        var tfQuestion = generateSingleTrueFalseQuestion(subject, statementType, i);
        gameTypeData.trueFalseQuestions.push(tfQuestion);
        
        questionsHtml += '<div class="question-item">';
        questionsHtml += '<h5>Question ' + i + '</h5>';
        questionsHtml += '<p><strong>' + tfQuestion.question + '</strong></p>';
        questionsHtml += '<div class="form-group">';
        questionsHtml += '<label class="radio-inline">';
        questionsHtml += '<input type="radio" name="tf_q' + i + '" value="true"> True';
        questionsHtml += '</label>';
        questionsHtml += '<label class="radio-inline">';
        questionsHtml += '<input type="radio" name="tf_q' + i + '" value="false"> False';
        questionsHtml += '</label>';
        questionsHtml += '</div>';
        questionsHtml += '<small class="text-muted">Correct answer: ' + (tfQuestion.correct_answer ? 'True' : 'False') + '</small>';
        questionsHtml += '<hr>';
        questionsHtml += '</div>';
    }
    
    questionsHtml += '</div></div>';
    $('#tf-questions-container').html(questionsHtml);
    
    gameData.questions = gameTypeData.trueFalseQuestions;
    $('#game_data').val(JSON.stringify(gameData));
}

function generateSingleTrueFalseQuestion(subject, statementType, questionId) {
    var statements = {
        science: [
            {statement: 'Water boils at 100 degrees Celsius', answer: true},
            {statement: 'The Earth is flat', answer: false},
            {statement: 'Plants need sunlight to grow', answer: true},
            {statement: 'Fish can breathe underwater because they have lungs', answer: false}
        ],
        math: [
            {statement: '2 + 2 = 4', answer: true},
            {statement: '5 × 3 = 16', answer: false},
            {statement: '10 ÷ 2 = 5', answer: true},
            {statement: '7 + 8 = 16', answer: false}
        ],
        general: [
            {statement: 'A day has 24 hours', answer: true},
            {statement: 'There are 13 months in a year', answer: false},
            {statement: 'Birds can fly', answer: true},
            {statement: 'All animals are mammals', answer: false}
        ]
    };
    
    var subjectStatements = statements[subject] || statements.general;
    var randomStatement = subjectStatements[Math.floor(Math.random() * subjectStatements.length)];
    
    return {
        id: questionId,
        question: randomStatement.statement,
        type: 'true_false',
        correct_answer: randomStatement.answer
    };
}

/**
 * Picture Puzzle Functions
 */
function generatePuzzleQuestions(totalQuestions) {
    var puzzleType = $('#puzzle-type').val();
    var imageCategory = $('#image-category').val();
    
    gameTypeData.puzzleQuestions = [];
    
    var questionsHtml = '<div class="panel panel-warning"><div class="panel-heading"><strong>Generated Picture Puzzles</strong></div><div class="panel-body">';
    questionsHtml += '<div class="alert alert-info">Picture puzzles will display images during gameplay. Default placeholder images will be used for demo.</div>';
    
    for (var i = 1; i <= totalQuestions; i++) {
        var puzzleQuestion = generateSinglePuzzleQuestion(puzzleType, imageCategory, i);
        gameTypeData.puzzleQuestions.push(puzzleQuestion);
        
        questionsHtml += '<div class="question-item">';
        questionsHtml += '<h5>Question ' + i + '</h5>';
        questionsHtml += '<p><strong>' + puzzleQuestion.question + '</strong></p>';
        questionsHtml += '<div class="text-center">';
        questionsHtml += '<div style="width: 150px; height: 150px; background: #f0f0f0; margin: 10px auto; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center;">';
        questionsHtml += '<i class="fa fa-image fa-3x text-muted"></i>';
        questionsHtml += '</div>';
        questionsHtml += '<small>Placeholder for: ' + puzzleQuestion.image_description + '</small>';
        questionsHtml += '</div>';
        
        if (puzzleQuestion.options) {
            questionsHtml += '<div class="row" style="margin-top: 15px;">';
            puzzleQuestion.options.forEach(function(option, index) {
                questionsHtml += '<div class="col-md-3">';
                questionsHtml += '<label class="radio-inline">';
                questionsHtml += '<input type="radio" name="puzzle_q' + i + '" value="' + index + '"> ' + option;
                questionsHtml += '</label>';
                questionsHtml += '</div>';
            });
            questionsHtml += '</div>';
        }
        
        questionsHtml += '<small class="text-muted">Correct answer: ' + puzzleQuestion.answer + '</small>';
        questionsHtml += '<hr>';
        questionsHtml += '</div>';
    }
    
    questionsHtml += '</div></div>';
    $('#puzzle-questions-container').html(questionsHtml);
    
    gameData.questions = gameTypeData.puzzleQuestions;
    $('#game_data').val(JSON.stringify(gameData));
}

function generateSinglePuzzleQuestion(puzzleType, imageCategory, questionId) {
    var animals = ['Cat', 'Dog', 'Bird', 'Fish', 'Rabbit', 'Horse'];
    var objects = ['Chair', 'Table', 'Book', 'Pencil', 'Phone', 'Computer'];
    var shapes = ['Circle', 'Square', 'Triangle', 'Rectangle', 'Pentagon', 'Hexagon'];
    
    var categoryItems = {
        animals: animals,
        objects: objects,
        shapes: shapes,
        nature: ['Tree', 'Flower', 'Mountain', 'River', 'Cloud', 'Sun']
    };
    
    var items = categoryItems[imageCategory] || animals;
    var selectedItem = items[Math.floor(Math.random() * items.length)];
    
    var question = '';
    var options = [];
    var answer = selectedItem;
    
    switch (puzzleType) {
        case 'identify':
            question = 'What is shown in this picture?';
            options = [selectedItem];
            // Add 3 wrong options
            var otherItems = items.filter(item => item !== selectedItem);
            for (var i = 0; i < 3 && i < otherItems.length; i++) {
                options.push(otherItems[i]);
            }
            options.sort(() => Math.random() - 0.5);
            break;
        case 'missing-piece':
            question = 'What piece is missing from this puzzle?';
            answer = 'Missing piece of ' + selectedItem;
            break;
        case 'sequence':
            question = 'What comes next in this sequence?';
            break;
    }
    
    return {
        id: questionId,
        question: question,
        type: 'picture_puzzle',
        image_description: selectedItem + ' image',
        options: options.length > 0 ? options : null,
        correct_answer: options.length > 0 ? options.indexOf(selectedItem) : 0,
        answer: answer
    };
}

/**
 * Vocabulary Builder Functions
 */
function generateVocabQuestions(totalQuestions) {
    var vocabLevel = $('#vocab-level').val();
    var vocabType = $('#vocab-type').val();
    var wordList = $('#word-list').val();
    
    gameTypeData.vocabQuestions = [];
    
    var questionsHtml = '<div class="panel panel-success"><div class="panel-heading"><strong>Generated Vocabulary Questions</strong></div><div class="panel-body">';
    
    var vocabularyWords = getVocabularyWords(vocabLevel, wordList);
    
    for (var i = 1; i <= totalQuestions; i++) {
        var vocabQuestion = generateSingleVocabQuestion(vocabularyWords, vocabType, i);
        gameTypeData.vocabQuestions.push(vocabQuestion);
        
        questionsHtml += '<div class="question-item">';
        questionsHtml += '<h5>Question ' + i + '</h5>';
        questionsHtml += '<p><strong>' + vocabQuestion.question + '</strong></p>';
        questionsHtml += '<div class="row">';
        
        vocabQuestion.options.forEach(function(option, index) {
            questionsHtml += '<div class="col-md-6">';
            questionsHtml += '<label class="radio-inline">';
            questionsHtml += '<input type="radio" name="vocab_q' + i + '" value="' + index + '"> ' + option;
            questionsHtml += '</label>';
            questionsHtml += '</div>';
        });
        
        questionsHtml += '</div>';
        questionsHtml += '<small class="text-muted">Correct answer: ' + vocabQuestion.options[vocabQuestion.correct_answer] + '</small>';
        questionsHtml += '<hr>';
        questionsHtml += '</div>';
    }
    
    questionsHtml += '</div></div>';
    $('#vocab-questions-container').html(questionsHtml);
    
    gameData.questions = gameTypeData.vocabQuestions;
    $('#game_data').val(JSON.stringify(gameData));
}

function getVocabularyWords(level, listType) {
    var vocabularyData = {
        beginner: {
            academic: [
                {word: 'happy', definition: 'feeling joy', synonyms: ['joyful', 'glad'], antonyms: ['sad', 'upset']},
                {word: 'big', definition: 'large in size', synonyms: ['large', 'huge'], antonyms: ['small', 'tiny']},
                {word: 'fast', definition: 'moving quickly', synonyms: ['quick', 'rapid'], antonyms: ['slow', 'sluggish']}
            ]
        },
        intermediate: {
            academic: [
                {word: 'analyze', definition: 'to examine in detail', synonyms: ['examine', 'study'], antonyms: ['ignore', 'overlook']},
                {word: 'compare', definition: 'to show similarities', synonyms: ['contrast', 'evaluate'], antonyms: ['ignore', 'separate']},
                {word: 'describe', definition: 'to give details about', synonyms: ['explain', 'portray'], antonyms: ['conceal', 'hide']}
            ]
        }
    };
    
    return vocabularyData[level]?.academic || vocabularyData.beginner.academic;
}

function generateSingleVocabQuestion(vocabularyWords, vocabType, questionId) {
    var randomWord = vocabularyWords[Math.floor(Math.random() * vocabularyWords.length)];
    var question = '';
    var options = [];
    var correctAnswer = 0;
    
    switch (vocabType) {
        case 'definitions':
            question = 'What does "' + randomWord.word + '" mean?';
            options = [randomWord.definition];
            // Add wrong definitions
            var otherWords = vocabularyWords.filter(w => w.word !== randomWord.word);
            for (var i = 0; i < 3 && i < otherWords.length; i++) {
                options.push(otherWords[i].definition);
            }
            options.sort(() => Math.random() - 0.5);
            correctAnswer = options.indexOf(randomWord.definition);
            break;
        case 'synonyms':
            question = 'Which word is a synonym for "' + randomWord.word + '"?';
            options = randomWord.synonyms.concat(['wrong1', 'wrong2']);
            options.sort(() => Math.random() - 0.5);
            correctAnswer = options.findIndex(opt => randomWord.synonyms.includes(opt));
            break;
        case 'antonyms':
            question = 'Which word is an antonym for "' + randomWord.word + '"?';
            options = randomWord.antonyms.concat(['wrong1', 'wrong2']);
            options.sort(() => Math.random() - 0.5);
            correctAnswer = options.findIndex(opt => randomWord.antonyms.includes(opt));
            break;
    }
    
    return {
        id: questionId,
        question: question,
        type: 'multiple_choice',
        options: options,
        correct_answer: correctAnswer
    };
}

// Initialize game type interface when category changes
$(document).ready(function() {
    $('#game_category_id').on('change', function() {
        var categoryId = $(this).val();
        var categoryName = $(this).find('option:selected').text();
        
        if (categoryId) {
            loadGameTypeInterface(categoryId, categoryName);
        } else {
            $('#questions-container').html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> Please select a game category to generate questions.</div>');
            $('#submit-btn').prop('disabled', true);
        }
    });
});