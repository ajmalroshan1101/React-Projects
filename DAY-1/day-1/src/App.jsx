// Important:
// For this project to work on CodeSandbox, image assets ("assets") folder
// must be stored in the public folder (as it's the case by default in this project)


//this is example for the custome component 
function Header(){

  return (
    <header>
        <img src="assets/react-core-concepts.png" alt="Stylized atom" />
        <h1>React Essentials</h1>
        <p>
          Fundamental React concepts you will need for almost any app you are
          going to build!
        </p>
      </header>

  )
}

function App() {
  return (
    <div>
      {/* Function name Spelling must Be correct */}
      {/* this is one way of calling the custom component in App function */}
      <Header></Header>

      {/* below is the second way of calling the custome Header 
      Component in App () here always remember to put the '/' */}

      {/* <Header/> */} 
      <main>
        <h2>Time to get started!</h2>
      </main>
    </div>
  );
}

export default App;
