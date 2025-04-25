document.addEventListener('DOMContentLoaded', () => {
    const startButton = document.getElementById('start-button');
    const startScreen = document.querySelector('.start-screen');
    const grid = document.querySelector('.grid');
    const scoreDisplay = document.getElementById('score');
    const width = 8;
    const squares = [];
    let score = 0;
  
    const candyImages = [
      'url(img/papel.png)',
      'url(img/metal.png)',
      'url(img/plastico.png)',
      'url(img/organico.png)',
      'url(img/vidro.png)',
    ];
  
    function createBoard() {
      for (let i = 0; i < width * width; i++) {
        const square = document.createElement('div');
        square.setAttribute('draggable', true);
        square.setAttribute('id', i);
        square.style.backgroundImage = candyImages[Math.floor(Math.random() * candyImages.length)];
        square.style.backgroundSize = 'cover';
        grid.appendChild(square);
        squares.push(square);
      }
    }

    startScreen.style.display = 'flex';
    grid.style.display = 'none';
  
    startButton.addEventListener('click', () => {
      startScreen.style.display = 'none';
      grid.style.display = 'flex';
      createBoard(); 
      startGame();   
    });
  
    function startGame() {
      let colorBeingDragged, colorBeingReplaced, squareIdBeingDragged, squareIdBeingReplaced;
  
      squares.forEach(square => square.addEventListener('dragstart', dragStart));
      squares.forEach(square => square.addEventListener('dragend', dragEnd));
      squares.forEach(square => square.addEventListener('dragover', dragOver));
      squares.forEach(square => square.addEventListener('dragenter', dragEnter));
      squares.forEach(square => square.addEventListener('dragleave', dragLeave));
      squares.forEach(square => square.addEventListener('drop', dragDrop));
  
      function dragStart() {
        colorBeingDragged = this.style.backgroundImage;
        squareIdBeingDragged = parseInt(this.id);
      }
  
      function dragOver(e) {
        e.preventDefault();
      }
  
      function dragEnter(e) {
        e.preventDefault();
      }
  
      function dragLeave() {}
  
      function dragDrop() {
        colorBeingReplaced = this.style.backgroundImage;
        squareIdBeingReplaced = parseInt(this.id);
        this.style.backgroundImage = colorBeingDragged;
        squares[squareIdBeingDragged].style.backgroundImage = colorBeingReplaced;
      }
  
      function dragEnd() {
        const validMoves = [
          squareIdBeingDragged - 1,
          squareIdBeingDragged - width,
          squareIdBeingDragged + 1,
          squareIdBeingDragged + width
        ];
        const validMove = validMoves.includes(squareIdBeingReplaced);
  
        if (squareIdBeingReplaced && validMove) {
          const isMatch = checkForMatches();
          if (!isMatch) {
            squares[squareIdBeingReplaced].style.backgroundImage = colorBeingReplaced;
            squares[squareIdBeingDragged].style.backgroundImage = colorBeingDragged;
          }
        } else {
          squares[squareIdBeingReplaced].style.backgroundImage = colorBeingReplaced;
          squares[squareIdBeingDragged].style.backgroundImage = colorBeingDragged;
        }
      }
  
      function moveDown() {
        for (let i = 0; i < 55; i++) {
          if (squares[i + width].style.backgroundImage === 'none') {
            squares[i + width].style.backgroundImage = squares[i].style.backgroundImage;
            squares[i].style.backgroundImage = 'none';
            if (squares[i].style.backgroundImage === 'none') {
              squares[i].style.backgroundImage = candyImages[Math.floor(Math.random() * candyImages.length)];
            }
          }
        }
      }
  
      function checkForMatches() {
        let matchFound = false;
        matchFound |= checkRowForMatch(3);
        matchFound |= checkColumnForMatch(3);
        return matchFound;
      }
  
      function checkRowForMatch(matchLength) {
        let scoreIncrement = 0;
        for (let i = 0; i < 64 - matchLength; i++) {
          let rowForMatch = Array.from({ length: matchLength }, (_, index) => i + index);
          let decidedColor = squares[i].style.backgroundImage;
          const isBlank = squares[i].style.backgroundImage === 'none';
  
          if (rowForMatch.every(index => squares[index].style.backgroundImage === decidedColor && !isBlank)) {
            scoreIncrement += matchLength;
            rowForMatch.forEach(index => squares[index].style.backgroundImage = 'none');
          }
        }
        if (scoreIncrement > 0) {
          score += scoreIncrement;
          scoreDisplay.innerHTML = score;
          return true;
        }
        return false;
      }
  
      function checkColumnForMatch(matchLength) {
        let scoreIncrement = 0;
        for (let i = 0; i < 64 - (matchLength - 1) * width; i++) {
          let columnForMatch = Array.from({ length: matchLength }, (_, index) => i + index * width);
          let decidedColor = squares[i].style.backgroundImage;
          const isBlank = squares[i].style.backgroundImage === 'none';
  
          if (columnForMatch.every(index => squares[index].style.backgroundImage === decidedColor && !isBlank)) {
            scoreIncrement += matchLength;
            columnForMatch.forEach(index => squares[index].style.backgroundImage = 'none');
          }
        }
        if (scoreIncrement > 0) {
          score += scoreIncrement;
          scoreDisplay.innerHTML = score;
          return true;
        }
        return false;
      }
  
      function updateGame() {
        moveDown();
        checkForMatches();
      }
  
      window.setInterval(updateGame, 100);
    }
  });
  