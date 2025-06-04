// npm install fs openai

import fs from 'fs';
import OpenAI from 'openai';

// Read the prompt.json file
const promptData = JSON.parse(fs.readFileSync('./prompt.json', 'utf8'));
const imageFile = promptData.image_file;
const promptText = promptData.prompt;

// OpenAI API key (same as in vectorstore/manager.js)
const OPENAI_KEY = 'YOUR OWN API KEY!';
const openai = new OpenAI({ apiKey: OPENAI_KEY });

async function analyzeImage() {
  try {
    // Read image as base64
    const imageBuffer = fs.readFileSync(imageFile);
    const base64Image = imageBuffer.toString('base64');

    // Call OpenAI API
    const response = await openai.chat.completions.create({
      model: "gpt-4o",
      messages: [
        {
          role: "user",
          content: [
            { type: "text", text: promptText },
            {
              type: "image_url",
              image_url: {
                url: `data:image/jpeg;base64,${base64Image}`
              }
            }
          ]
        }
      ],
      max_tokens: 1000
    });

    // Get and output the response
    const result = response.choices[0].message.content.trim();
    console.log(result);
  } catch (error) {
    console.error('Error analyzing image:', error);
    console.log('nothing'); // Return "nothing" on error
  }
}

analyzeImage();