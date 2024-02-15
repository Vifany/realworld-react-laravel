import React from 'react';
import logo from './logo.svg';
import './App.css';
import Header from './Components/header/header'
import Footer from './Components/footer/footer';
import Homepage from './Components/home-page/home-page';

function App() {
  return (
    
    <div className="App">
      <Header/>
      <Homepage/>
      <Footer/>
    </div>
  );
}

export default App;
