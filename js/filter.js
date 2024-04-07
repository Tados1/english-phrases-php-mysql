const input = document.querySelector(".search_input");
const allOnePhrase = document.querySelectorAll(".one-phrase");
const allOnePhraseArray = Array.from(allOnePhrase);
const allPhrasesDiv = document.querySelector(".all-phrases");

const phrasesObjects = allOnePhraseArray.map((onePhrase, index) => {
    const slovakContent = onePhrase.querySelector('.phrases p:nth-of-type(1)').textContent;
    const englishContent = onePhrase.querySelector('.phrases p:nth-of-type(2)').textContent;
    return {
        id: index,
        phraseHTML: onePhrase.innerHTML,
        slovakPhrase: slovakContent,
        englishPhrase: englishContent
    };
});

input.addEventListener("input", () => {
    const inputText = input.value.toLowerCase();
    const filteredPhrases = phrasesObjects.filter((onePhrase) => {
        return onePhrase.slovakPhrase.toLowerCase().includes(inputText) ||
               onePhrase.englishPhrase.toLowerCase().includes(inputText);
    });

    allPhrasesDiv.innerHTML = "";

    filteredPhrases.map((onePhrase) => {
        const newDiv = document.createElement("div");
        newDiv.classList.add("one-phrase");

        newDiv.innerHTML = onePhrase.phraseHTML;

        allPhrasesDiv.append(newDiv);
    });
});