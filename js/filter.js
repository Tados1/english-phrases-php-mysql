const input = document.querySelector(".search_input");
const allOnePhrase = document.querySelectorAll(".one-phrase");
const allOnePhraseArray = Array.from(allOnePhrase);
const allPhrasesDiv = document.querySelector(".all-phrases");

const phrasesObjects = allOnePhraseArray.map((onePhrase, index) => {
    const slovakContent = onePhrase.querySelector('.phrases p:nth-of-type(1)').textContent;
    const englishContent = onePhrase.querySelector('.phrases p:nth-of-type(2)').textContent;
    const isHidden = onePhrase.classList.contains('hide');
    return {
        id: index,
        phraseHTML: onePhrase.innerHTML,
        slovakPhrase: slovakContent,
        englishPhrase: englishContent,
        isHidden: isHidden
    };
});

input.addEventListener("input", () => {
    const inputText = input.value.toLowerCase();
    const filteredPhrases = phrasesObjects.filter((onePhrase) => {
        return onePhrase.slovakPhrase.toLowerCase().includes(inputText) ||
               onePhrase.englishPhrase.toLowerCase().includes(inputText);
    });

    allPhrasesDiv.innerHTML = "";

    filteredPhrases.forEach((onePhrase) => {
        const newDiv = document.createElement("div");
        newDiv.classList.add("one-phrase");

        if(onePhrase.isHidden) {
            newDiv.classList.add("hide");
        }

        newDiv.innerHTML = onePhrase.phraseHTML;

        allPhrasesDiv.append(newDiv);
    });
});