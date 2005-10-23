// SocketException class


#ifndef SocketException_class
#define SocketException_class

#include <string>

class SocketException {
	private:
		std::string m_s;

	public:
		SocketException(std::string s) : m_s(s) {};
		~SocketException() {};

		std::string description() { return m_s; }
};

#endif
