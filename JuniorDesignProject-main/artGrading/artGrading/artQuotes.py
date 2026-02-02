#from something import something_else
import random

def getArtQuote():
    art_quotes = ['Art is not what you see, but what you make others see.', 'There is no must in art because art is free.', 'A beautiful body perishes, but a work of art dies not.', 'Art is the journey of a free soul.', 'As my artist\'s statement explains, my work is utterly incomprehensible and is therefore full of deep significance.', 'A work of art that did not begin in emotion is not art.', 'Art is an international lauguage, understood by all.', 'It\'s not what you look at that matters, it\'s what you see.', 'I saw the angel in the marble and carved until I set him free.']
    art_quote_authors = ['Edgar Degas', 'Wassily Kandinsky', 'Leonardo da Vinci', 'Alev Oguz', 'Calvin & Hobbes', 'Paul Cezanne', 'Igor Babailov', 'Henry David Thoreau', 'Michelangelo']

    i = random.randint(0, len(art_quotes) - 1)
    return art_quotes[i], art_quote_authors[i]