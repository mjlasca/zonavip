const questionsContainer = document.getElementById("questionsContainer");
const quizForm = document.getElementById("quizForm");
const nameQuiz = document.getElementById("name");
const detailQuiz = document.getElementById("detail");
const timeQuiz = document.getElementById("time");
const questionNumberQuiz = document.getElementById("question_number");
const questionVisibleQuiz = document.getElementById("question_visible");
const questionPassQuiz = document.getElementById("question_pass");
const regQuiz = document.getElementById("reg");
let countQuestions = 0;


function saveQuestion(question) {
  const questions = JSON.parse(localStorage.getItem("questions")) || [];
  questions.push(question);
}

questionsContainer.addEventListener("click", function (event) {
  if (event.target.classList.contains("question-remove")) {
    event.target.closest(".question").remove();
  }
});

function addQuestion(existingQuestion = null, questions = null) {
  const questionDiv = document.createElement("div");
  questionDiv.className = "question";

  const removeQuestion = document.createElement("a");
  removeQuestion.className = "question-remove";
  removeQuestion.href = "#";
  removeQuestion.innerText = "x";

  const questionInput = document.createElement("input");
  questionInput.type = "text";
  questionInput.name = "question";
  questionInput.placeholder = "Ingrese la pregunta";
  if (questions != null) questionInput.value = questions.q;

  const answerSelect = document.createElement("select");
  answerSelect.name = "answer_select[]";
  const options = ["- Respuest correcta -", "1", "2", "3", "4"];
  answerSelect.classList.add("answer-select");
  answerSelect.classList.add("form-control");
  answerSelect.required = true;
  options.forEach((optionText, index) => {
    const option = document.createElement("option");
    option.value = index;
    option.text = optionText;
    answerSelect.appendChild(option);
  });
  if (questions != null) answerSelect.value = questions.correct;

  if (existingQuestion) questionInput.value = existingQuestion.question;

  const answersDiv = document.createElement("div");
  answersDiv.className = "answers";

  for (let i = 0; i < 4; i++) {
    const answerDiv = document.createElement("div");

    const answerInput = document.createElement("input");
    answerInput.type = "text";
    answerInput.className = "answer-input";
    answerInput.name = `answer${i}[]`;
    answerInput.placeholder = `Respuesta ${i + 1}`;
    if (questions != null) answerInput.value = questions.answer[i];
    if (existingQuestion) answerInput.value = existingQuestion.answers[i].text;
    answerDiv.appendChild(answerInput);
    answersDiv.appendChild(answerDiv);
  }

  questionDiv.appendChild(removeQuestion);
  questionDiv.appendChild(questionInput);
  questionDiv.appendChild(answersDiv);
  questionDiv.appendChild(answerSelect);

  questionsContainer.appendChild(questionDiv);

  if (!existingQuestion) {
    const newQuestion = {
      question: "",
      answers: [{ text: "" }, { text: "" }, { text: "" }, { text: "" }],
      correct: null,
    };
    saveQuestion(newQuestion);
  }
}

function confirmDelete(reg, url) {
  if (confirm("¿Está segur@ de eliminar la prueba?")) {
    window.location.href = url + "&delete_quiz=" + reg;
  }
}

if(quizForm){
  quizForm.addEventListener("submit", function (event) {
    event.preventDefault();
    let questions = { q: [], quiz: [] };
  
    const questionDivs = document.querySelectorAll(".question");
    let errors = 0;
  
    questionDivs.forEach((questionDiv, index) => {
      const questionInput = questionDiv.querySelector('input[name="question"]');
      const answerInputs = questionDiv.querySelectorAll(".answer-input");
      const correctInputs = questionDiv.querySelectorAll(".answer-select");
  
      if (questionInput.classList.contains("error-input"))
        questionInput.classList.remove("error-input");
  
      if (!questionInput.value) {
        //alert(`Question ${index + 1} is empty`);
        errors++;
        questionInput.classList.add("error-input");
        return;
      }
  
      const answers = [];
  
      answerInputs.forEach((input, i) => {
        if (input.classList.contains("error-input"))
          input.classList.remove("error-input");
  
        if (!input.value) {
          //alert(`Answer ${i + 1} in question ${index + 1} is empty`);
          input.classList.add("error-input");
          errors++;
          return;
        }
        answers.push({ text: input.value });
      });
  
      correctInputs.forEach((sele, i) => {
        if (sele.value == "") errors++;
        else {
          questions.q.push({
            question: questionInput.value,
            answers,
            correct: sele.value,
          });
        }
      });
    });
  
    if (errors < 1) {
      //localStorage.setItem('questions', JSON.stringify(questions));
      sendQuestions(questions);
    }
  });
}


async function getQuiz(reg, quizuser = false) {
  const rawResponse = fetch("index.php?page=quizzes&reg_quiz=" + reg, {
    method: "GET",
  });
  const result = await rawResponse.then((response) => {
    return response.json();
  });
  if (result) {
    buildQuiz(result, quizuser);
  }
}

function buildQuiz(json, quizuser = false) {
    if(quizuser === false){
        regQuiz.value = json.quiz.reg;
        nameQuiz.value = json.quiz.name;
        detailQuiz.value = json.quiz.detail;
        timeQuiz.value = json.quiz.limit_time;
        questionNumberQuiz.value = json.quiz.question_number;
        questionVisibleQuiz.value = json.quiz.question_visible;
        questionPassQuiz.value = json.quiz.question_pass;
    }
  
    if (questionsContainer) {
      questionsContainer.innerHTML = "";
    }
    json.questions.forEach((element) => {
      if(quizuser)
        addQuestionUser(element);
      else
        addQuestion(null, element);
    });
}

function resetInputs() {
  regQuiz.value = "";
  nameQuiz.value = "";
  detailQuiz.value = "";
  timeQuiz.value = "";
  questionNumberQuiz.value = "";
  questionVisibleQuiz.value = "";
  questionPassQuiz.value = "";
  if (questionsContainer) {
    questionsContainer.innerHTML = "";
  }
}

async function sendQuestions(questions) {
  questions.quiz = {
    reg: regQuiz.value,
    name: nameQuiz.value,
    detail: detailQuiz.value,
    time: timeQuiz.value,
    question_number: questionNumberQuiz.value,
    question_visible: questionVisibleQuiz.value,
    question_pass: questionPassQuiz.value,
  };

  const rawResponse = fetch("index.php?page=quizzes", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ questions }),
  });

  const result = await rawResponse.then((response) => {
    return response.json();
  });

  if (result) {
    regQuiz.value = result.reg;
  }
}

function questionStart(url) {
  const questionStartContent = document.querySelector('.question-start');
    questionStartContent.classList.remove('hide');  
  const startButton = document.querySelector('.start-btn');
    startButton.classList.add('hide');
  
    window.location.href = url+'&start=true';
}


function getParam(parametro) {
    const url = new URL(window.location.href);
    const valorParametro = url.searchParams.get(parametro);
    return valorParametro;
}


function addQuestionUser(questions = null) {
  const questionDiv = document.createElement("div");
  questionDiv.className = "question";
  const questionInput = document.createElement("h4");
  questionInput.textContent = questions.q;

  const answersDiv = document.createElement("div");
  answersDiv.className = "answers";



  for (let i = 0; i < 4; i++) {
    const answerDiv = document.createElement("div");
    answerDiv.className = 'answer-radios';
    const answerInput = document.createElement("input");
    answerInput.type = "radio";
    answerInput.className = "answer-input";
    answerInput.name = `answer${countQuestions}[]`;
    const labelAnswer = document.createElement("label");
    labelAnswer.textContent = questions.answer[i];
    answerDiv.appendChild(answerInput);
    answerDiv.appendChild(labelAnswer);
    answersDiv.appendChild(answerDiv);
  }
  countQuestions++;
  questionDiv.appendChild(questionInput);
  questionDiv.appendChild(answersDiv);
  questionsContainer.appendChild(questionDiv);
  

}