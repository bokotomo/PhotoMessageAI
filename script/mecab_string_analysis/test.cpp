#include <iostream>
#include <string>
#include <mecab.h>
using namespace std;

#define CHECK(eval) if (! eval) { \
   const char *e = tagger ? tagger->what() : MeCab::getTaggerError(); \
   std::cerr << "Exception:" << e << std::endl; \
   delete tagger; \
   return -1; }

int main (int argc, char **argv) {
  char input[1024] = "太郎は次郎が持っている本を花子に渡した。!";
  string GetText;
  GetText = argv[1];

  std::cout << "INPUT: " << GetText << std::endl;
  
  MeCab::Tagger *tagger = MeCab::createTagger("");
  CHECK(tagger);
  const char *result = tagger->parse(input);
  std::cout << result; 

  // Dictionary info.
  std::cout << "--------------------" << std::endl;
  const MeCab::DictionaryInfo *d = tagger->dictionary_info();
  for (; d; d = d->next) {
    std::cout << "filename: " <<  d->filename << std::endl;
    std::cout << "charset: " <<  d->charset << std::endl;
    std::cout << "size: " <<  d->size << std::endl;
    std::cout << "type: " <<  d->type << std::endl;
    std::cout << "lsize: " <<  d->lsize << std::endl;
    std::cout << "rsize: " <<  d->rsize << std::endl;
    std::cout << "version: " <<  d->version << std::endl;
  }

  delete tagger;

  return 0;
}